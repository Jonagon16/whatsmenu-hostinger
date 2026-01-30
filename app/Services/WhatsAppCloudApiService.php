<?php

namespace App\Services;

use App\Models\BotConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppCloudApiService
{
    protected $graphVersion;

    public function __construct()
    {
        $this->graphVersion = env('WHATSAPP_GRAPH_VERSION', 'v20.0');
    }

    /**
     * Enviar mensaje de texto simple.
     */
    public function sendText(BotConfig $botConfig, string $to, string $text)
    {
        return $this->sendMessage($botConfig, $to, [
            'type' => 'text',
            'text' => ['body' => $text]
        ]);
    }

    /**
     * Enviar mensaje con botones interactivos (máximo 3 botones).
     */
    public function sendButtons(BotConfig $botConfig, string $to, string $bodyText, array $buttons, ?string $headerText = null, ?string $footerText = null)
    {
        $interactive = [
            'type' => 'button',
            'body' => ['text' => $bodyText],
            'action' => [
                'buttons' => $buttons // Array de ['type' => 'reply', 'reply' => ['id' => '...', 'title' => '...']]
            ]
        ];

        if ($headerText) {
            $interactive['header'] = [
                'type' => 'text',
                'text' => $headerText
            ];
        }

        if ($footerText) {
            $interactive['footer'] = ['text' => $footerText];
        }

        return $this->sendMessage($botConfig, $to, [
            'type' => 'interactive',
            'interactive' => $interactive
        ]);
    }

    /**
     * Enviar mensaje de lista (hasta 10 opciones).
     */
    public function sendList(BotConfig $botConfig, string $to, string $bodyText, string $buttonLabel, array $sections, ?string $headerText = null, ?string $footerText = null)
    {
        $interactive = [
            'type' => 'list',
            'body' => ['text' => $bodyText],
            'action' => [
                'button' => $buttonLabel,
                'sections' => $sections // Array de secciones con rows
            ]
        ];

        if ($headerText) {
            $interactive['header'] = [
                'type' => 'text',
                'text' => $headerText
            ];
        }

        if ($footerText) {
            $interactive['footer'] = ['text' => $footerText];
        }

        return $this->sendMessage($botConfig, $to, [
            'type' => 'interactive',
            'interactive' => $interactive
        ]);
    }

    /**
     * Método base para enviar peticiones a Graph API.
     */
    protected function sendMessage(BotConfig $botConfig, string $to, array $messageData)
    {
        $url = "https://graph.facebook.com/{$this->graphVersion}/{$botConfig->whatsapp_phone_number_id}/messages";

        $payload = array_merge([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
        ], $messageData);

        try {
            $response = Http::withToken($botConfig->whatsapp_access_token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->failed()) {
                Log::error('WhatsApp API Error', [
                    'tenant_id' => $botConfig->id,
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'payload' => $payload
                ]);
                return false;
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('WhatsApp API Exception', [
                'tenant_id' => $botConfig->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
