<?php

namespace App\Http\Controllers;

use App\Models\BotConfig;
use App\Services\IncomingMessageHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    protected $messageHandler;

    public function __construct(IncomingMessageHandler $messageHandler)
    {
        $this->messageHandler = $messageHandler;
    }
    /**
     * Verifica el webhook con WhatsApp (Meta).
     * Soporta múltiples tenants buscando el token en la BD.
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token) {
            // Buscamos si existe ALGÚN bot con este token de verificación
            $exists = BotConfig::where('whatsapp_verify_token', $token)->exists();

            if ($exists) {
                Log::info("Webhook verificado correctamente para token: $token");
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            } else {
                Log::warning("Intento de verificación fallido. Token no encontrado: $token");
            }
        }

        return response()->json(['error' => 'Forbidden', 'message' => 'Verification failed'], 403);
    }

    /**
     * Recibe eventos de WhatsApp.
     * Identifica el tenant y procesa el mensaje.
     */
    public function receive(Request $request)
    {
        Log::info('WEBHOOK DEBUG HEADERS', [
            'x-hub-signature-256' => $request->header('X-Hub-Signature-256'),
            'content-type' => $request->header('Content-Type'),
            'is_simulation' => $request->header('X-Is-Simulation'),
        ]);

        $payload = $request->all();
        
        // Extraer phone_number_id para identificar al tenant
        $phoneNumberId = $this->extractPhoneNumberId($payload);

        if (!$phoneNumberId) {
            // Si es un evento de estado (status) a veces la estructura varía, 
            // pero generalmente trae metadata.
            // Si no podemos identificar el tenant, logueamos y respondemos 200 para no bloquear a Meta.
            Log::warning('WhatsApp Webhook: No se pudo extraer phone_number_id', ['payload' => $payload]);
            return response()->json(['status' => 'ignored_no_id'], 200);
        }

        // Buscar configuración del Bot
        $botConfig = BotConfig::where('whatsapp_phone_number_id', $phoneNumberId)->first();

        if (!$botConfig) {
            Log::warning("WhatsApp Webhook: Tenant no encontrado para phone_number_id: $phoneNumberId");
            return response()->json(['status' => 'ignored_tenant_not_found'], 200);
        }

        // Validar firma si el tenant tiene app secret configurado
        if ($botConfig->whatsapp_app_secret) {
            if (!$this->verifySignature($request, $botConfig->whatsapp_app_secret)) {
                Log::error("WhatsApp Webhook: Firma inválida para tenant {$botConfig->id}");
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        // Parsear datos básicos (Fase 2)
        $this->processPayload($payload, $botConfig);

        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Extrae el phone_number_id del payload.
     */
    private function extractPhoneNumberId($payload)
    {
        // Estructura común: entry[0].changes[0].value.metadata.phone_number_id
        return $payload['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'] ?? null;
    }

    /**
     * Valida la firma X-Hub-Signature-256 usando el secreto del tenant.
     */
    private function verifySignature(Request $request, $appSecret)
    {
        $signature = $request->header('X-Hub-Signature-256');

        if (!$signature) {
            return false;
        }

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $appSecret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Procesa el payload (Fase 2: Identificación básica).
     * Aquí se despacharía el Job en el futuro.
     */
    private function processPayload($payload, $botConfig)
    {
        $entry = $payload['entry'][0] ?? [];
        $changes = $entry['changes'][0] ?? [];
        $value = $changes['value'] ?? [];
        
        // Ver si hay mensajes
        if (isset($value['messages'])) {
            // Extraer info de contacto si viene
            $contactName = null;
            if (isset($value['contacts'][0]['profile']['name'])) {
                $contactName = $value['contacts'][0]['profile']['name'];
            }

            foreach ($value['messages'] as $message) {
                // Delegar al Job (Asíncrono)
                // En local, si QUEUE_CONNECTION=sync, se ejecuta al instante.
                ProcessWhatsAppMessage::dispatch($botConfig, $message);
            }
        } elseif (isset($value['statuses'])) {
            // Actualización de estado de mensajes (sent, delivered, read)
            // TODO: Implementar manejo de estados
            Log::info("Actualización de estado para Tenant ID: {$botConfig->id}", [
                'statuses' => $value['statuses']
            ]);
        }
    }
}
