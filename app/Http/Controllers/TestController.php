<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\BotConfig;
use App\Models\Conversation;
use App\Models\Message;

class TestController extends Controller
{
    public function index()
    {
        $user = User::where('email', 'jona@gmail.com')->first();
        $bot = $user ? BotConfig::where('user_id', $user->id)->first() : null;
        
        $logs = [];
        if ($bot) {
            // Obtener últimos mensajes de conversaciones asociadas a este bot
            $conversations = Conversation::where('bot_config_id', $bot->id)->pluck('id');
            $logs = Message::whereIn('conversation_id', $conversations)
                           ->orderBy('created_at', 'desc')
                           ->take(20)
                           ->get();
        }

        return view('tests.jona', compact('user', 'bot', 'logs'));
    }

    public function reset()
    {
        try {
            Artisan::call('db:seed', ['--class' => 'JonaTestSeeder']);
            return redirect()->back()->with('success', 'Datos de Jona reseteados correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al resetear: ' . $e->getMessage());
        }
    }
    
    public function simulateWebhook(Request $request)
    {
        $text = $request->input('text', 'hola');
        $phoneId = $request->input('phone_id', '123456789_TEST_ID');
        $userPhone = $request->input('user_phone', '5491100000000');

        // Construir payload simulado de Meta
        $payload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'WHATSAPP_BUSINESS_ACCOUNT_ID',
                    'changes' => [
                        [
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => '1234567890',
                                    'phone_number_id' => $phoneId,
                                ],
                                'contacts' => [
                                    [
                                        'profile' => ['name' => 'Cliente Test'],
                                        'wa_id' => $userPhone,
                                    ]
                                ],
                                'messages' => [
                                    [
                                        'from' => $userPhone,
                                        'id' => 'wamid.test.' . uniqid(),
                                        'timestamp' => time(),
                                        'type' => 'text',
                                        'text' => ['body' => $text]
                                    ]
                                ]
                            ],
                            'field' => 'messages'
                        ]
                    ]
                ]
            ]
        ];

        // Buscar el bot para la firma
        $bot = BotConfig::where('whatsapp_phone_number_id', $phoneId)->first();
        
        $server = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ];

        $jsonPayload = json_encode($payload);

        if ($bot && $bot->whatsapp_app_secret) {
            $signature = 'sha256=' . hash_hmac('sha256', $jsonPayload, $bot->whatsapp_app_secret);
            $server['HTTP_X_HUB_SIGNATURE_256'] = $signature;
        }

        // Crear una instancia de Request compatible con lo que espera el controlador
        // Pasamos el jsonPayload como content (7mo parámetro)
        $webhookRequest = Request::create(
            '/api/webhooks/whatsapp', 
            'POST', 
            [], 
            [], 
            [], 
            $server,
            $jsonPayload
        );
        
        $controller = app(\App\Http\Controllers\WhatsAppWebhookController::class);
        $response = $controller->receive($webhookRequest);

        return redirect()->back()->with('success', 'Webhook simulado: ' . $text . ' (Status: ' . $response->getStatusCode() . ')');
    }
}
