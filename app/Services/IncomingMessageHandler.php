<?php

namespace App\Services;

use App\Events\ConversationCreated;
use App\Events\ConversationUpdated;
use App\Events\MessageReceived;
use App\Events\MessageSent;
use App\Models\BotConfig;
use App\Models\Conversation;
use App\Models\Menu;
use App\Models\MenuNode;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class IncomingMessageHandler
{
    protected $whatsappService;

    public function __construct(WhatsAppCloudApiService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Maneja un mensaje entrante de WhatsApp.
     */
    public function handle(BotConfig $botConfig, array $messageData, ?string $contactName = null)
    {
        $waId = $messageData['from'];
        $type = $messageData['type'];
        $messageId = $messageData['id'] ?? null;

        // 0. Idempotencia: Verificar si el mensaje ya fue procesado
        if ($messageId && Message::where('whatsapp_message_id', $messageId)->exists()) {
            Log::info("Mensaje duplicado ignorado: $messageId");
            return;
        }

        $body = $this->extractBody($messageData);

        // 1. Obtener o crear conversación
        $conversation = Conversation::firstOrCreate(
            ['bot_config_id' => $botConfig->id, 'wa_id' => $waId],
            ['current_node_id' => 'root', 'metadata' => []]
        );

        // Actualizar nombre si viene y no lo tenemos o cambió
        if ($contactName && $conversation->display_name !== $contactName) {
            $conversation->display_name = $contactName;
            $conversation->save();
        }

        if ($conversation->wasRecentlyCreated) {
            ConversationCreated::dispatch($conversation);
        }

        // 2. Guardar mensaje entrante
        $this->logMessage($conversation, 'inbound', $type, $body, $messageData['id'] ?? null, $messageData);

        // 3. Procesar lógica de flujo (Router)
        $this->processFlow($botConfig, $conversation, $type, $body);
    }

    /**
     * Lógica principal de enrutamiento del bot.
     */
    protected function processFlow(BotConfig $botConfig, Conversation $conversation, string $type, $input)
    {
        // Resetear si escribe "hola" o "menu"
        if ($type === 'text' && in_array(strtolower(trim($input)), ['hola', 'inicio', 'menu', 'volver'])) {
            $conversation->current_node_id = 'root';
            $conversation->save();
        }

        // Obtener el menú activo del bot (Tenant)
        // Asumimos que el usuario tiene un menú activo relacionado.
        // Si no hay relación directa en BotConfig, buscamos por user_id.
        $menu = Menu::where('user_id', $botConfig->user_id)->where('is_active', true)->first();

        if (!$menu) {
            $this->sendAndLog($botConfig, $conversation, "⚠️ Este bot no tiene un menú activo configurado.");
            return;
        }

        // Buscar el nodo actual
        $currentNodeSlug = $conversation->current_node_id ?? 'root';
        
        // Si estamos en root, buscamos el nodo root del menú
        // Ojo: Si el nodo no existe en la DB, fallará.
        $node = MenuNode::where('menu_id', $menu->id)->where('slug', $currentNodeSlug)->first();

        // Si no existe el nodo actual (ej: borraron el nodo), volver a root
        if (!$node) {
            $node = MenuNode::where('menu_id', $menu->id)->where('slug', 'root')->first();
            if (!$node) {
                // Si ni siquiera existe root, mensaje de error genérico
                $this->sendAndLog($botConfig, $conversation, "Bienvenido a " . $botConfig->bot_name . ". Estamos configurando nuestro menú.");
                return;
            }
            // Actualizamos conv a root
            $conversation->update(['current_node_id' => 'root']);
        }

        // --- Lógica de Transición de Estado ---
        // Si el usuario envió una respuesta (interactiva o texto) que implica moverse
        // verificamos si debemos cambiar de nodo ANTES de renderizar.
        
        $nextNodeSlug = null;

        if ($type === 'interactive') {
            // Respuesta a botón o lista
            $interactiveType = $input['type'] ?? null;
            $selectedId = null;

            if ($interactiveType === 'button_reply') {
                $selectedId = $input['button_reply']['id'] ?? null;
            } elseif ($interactiveType === 'list_reply') {
                $selectedId = $input['list_reply']['id'] ?? null;
            }

            // El ID del botón debería ser el slug del siguiente nodo (o una acción)
            if ($selectedId) {
                $nextNodeSlug = $selectedId;
            }

        } elseif ($type === 'text') {
            // Si es texto, tratamos de matchear con las opciones del nodo actual (si las hubiera)
            // Esto es más complejo (NLP simple o coincidencia exacta de números/texto)
            // Por ahora, asumimos que texto 'hola' resetea (ya manejado arriba) 
            // y texto normal no navega a menos que implementemos lógica de "Opción 1", "Opción 2".
            // TODO: Implementar selección por número.
        }

        // Si hay un cambio de nodo detectado
        if ($nextNodeSlug) {
            // Verificar si el nodo destino existe
            $targetNode = MenuNode::where('menu_id', $menu->id)->where('slug', $nextNodeSlug)->first();
            
            if ($targetNode) {
                $node = $targetNode;
                $conversation->update(['current_node_id' => $nextNodeSlug]);
            } else {
                // Si el ID no es un nodo, podría ser una acción especial (ej: link, pedido)
                // Por ahora, si no es nodo, nos quedamos donde estamos y avisamos.
                // Opcional: Manejar acciones especiales aquí.
            }
        }

        // --- Renderizar el Nodo Actual (o el Nuevo) ---
        $this->renderNode($botConfig, $conversation, $node);
    }

    /**
     * Envía la respuesta correspondiente a un nodo.
     */
    protected function renderNode(BotConfig $botConfig, Conversation $conversation, MenuNode $node)
    {
        $options = $node->options; // Relación hasMany
        $bodyText = $node->body_text;
        $header = $node->title;
        $footer = $node->footer_text;

        // Caso 1: Solo texto (sin opciones)
        if ($options->isEmpty()) {
            $this->sendAndLog($botConfig, $conversation, $bodyText);
            return;
        }

        // Caso 2: Botones (hasta 3)
        if ($node->type === 'buttons' || ($node->type === 'text' && $options->count() <= 3)) {
            $buttons = [];
            foreach ($options as $opt) {
                // Usamos el next_node_slug como ID del botón. 
                // Si es nulo, usaremos algo genérico o el ID de la opción.
                $id = $opt->next_node_slug ?? ('opt_' . $opt->id);
                $buttons[] = [
                    'type' => 'reply',
                    'reply' => [
                        'id' => $id,
                        'title' => substr($opt->label, 0, 20) // Limitación de WhatsApp: 20 chars
                    ]
                ];
            }
            
            $this->whatsappService->sendButtons(
                $botConfig, 
                $conversation->wa_id, 
                $bodyText, 
                $buttons, 
                $header, 
                $footer
            );
            // Log outgoing (simplificado)
            $this->logMessage($conversation, 'outbound', 'interactive', "Enviado menú botones: $bodyText");
            return;
        }

        // Caso 3: Lista (hasta 10)
        // WhatsApp requiere secciones para listas.
        $rows = [];
        foreach ($options as $opt) {
            $id = $opt->next_node_slug ?? ('opt_' . $opt->id);
            $row = [
                'id' => $id,
                'title' => substr($opt->label, 0, 24) // Limitación 24 chars
            ];
            if ($opt->description) {
                $row['description'] = substr($opt->description, 0, 72);
            }
            $rows[] = $row;
        }

        $sections = [
            [
                'title' => 'Opciones',
                'rows' => $rows
            ]
        ];

        $this->whatsappService->sendList(
            $botConfig,
            $conversation->wa_id,
            $bodyText,
            'Ver Opciones', // Texto del botón que abre la lista
            $sections,
            $header,
            $footer
        );
        $this->logMessage($conversation, 'outbound', 'interactive', "Enviado menú lista: $bodyText");
    }

    /**
     * Helper para guardar mensajes en DB.
     */
    protected function logMessage(Conversation $conversation, $direction, $type, $body, $waMessageId = null, $payload = null)
    {
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'direction' => $direction,
            'type' => $type,
            'body' => is_array($body) ? json_encode($body) : $body,
            'whatsapp_message_id' => $waMessageId,
            'status' => $direction === 'inbound' ? 'received' : 'sent',
            'payload' => $payload
        ]);

        // Actualizar timestamp última interacción
        $conversation->touch();
        
        // Dispatch Events
        if ($direction === 'inbound') {
            MessageReceived::dispatch($message);
        } else {
            MessageSent::dispatch($message);
        }
        
        ConversationUpdated::dispatch($conversation);
    }

    /**
     * Helper para enviar texto simple y loguear.
     */
    protected function sendAndLog(BotConfig $botConfig, Conversation $conversation, $text)
    {
        $this->whatsappService->sendText($botConfig, $conversation->wa_id, $text);
        $this->logMessage($conversation, 'outbound', 'text', $text);
    }

    /**
     * Extrae el contenido legible del mensaje.
     */
    protected function extractBody($messageData)
    {
        $type = $messageData['type'];
        if ($type === 'text') {
            return $messageData['text']['body'] ?? '';
        }
        if ($type === 'interactive') {
            return $messageData['interactive'] ?? [];
        }
        return '';
    }
}
