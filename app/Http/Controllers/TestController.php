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

        // Llamar internamente al controlador del Webhook
        // Instanciamos el controlador manualmente o hacemos una petición HTTP interna.
        // Para pruebas rápidas, petición HTTP interna a nuestra propia API.
        
        $request = Request::create('/api/webhooks/whatsapp', 'POST', $payload);
        
        // Importante: Headers para firma si fuera necesario, pero en local a veces lo saltamos
        // o configuramos el test para que use un secreto dummy.
        
        $controller = app(\App\Http\Controllers\WhatsAppWebhookController::class);
        $response = $controller->receive($request);

        return redirect()->back()->with('success', 'Webhook simulado: ' . $text . ' (Status: ' . $response->getStatusCode() . ')');
    }
}
