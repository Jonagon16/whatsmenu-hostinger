<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handleWhatsapp(Request $request)
    {
        // GET Request - Verification
        if ($request->isMethod('get')) {
            $mode = $request->query('hub_mode');
            $token = $request->query('hub_verify_token');
            $challenge = $request->query('hub_challenge');
            
            // Accept any token for now as per legacy
            if ($mode === 'subscribe' && $token) {
                return response($challenge, 200);
            }
            return response('Forbidden', 403);
        }

        // POST Request - Incoming Message
        $data = $request->all();
        $entry = $data['entry'][0] ?? null;
        $changes = $entry['changes'][0] ?? null;
        $value = $changes['value'] ?? null;
        $messages = $value['messages'] ?? null;
        $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;

        if (!$messages) {
            return response('OK', 200);
        }

        $messageData = $messages[0];
        $from = $messageData['from'];
        $text = $messageData['text']['body'] ?? '';

        // Find User by phone_number_id
        // In legacy, this was encrypted. For now, assume plain text or implement similar search.
        // Simplified: Direct match (Change this if you migrate existing encrypted data)
        $user = \App\Models\User::where('whatsapp_phone_number_id', $phoneNumberId)->first();
        
        // If not found, try to search all (legacy style - inefficient but compatible)
        if (!$user) {
             // Logic to iterate all users and decrypt would go here if needed.
             // For this migration, we assume new setup or re-keyed data.
             return response()->json(['error' => 'User not found'], 404);
        }

        // Get Active Menu
        $activeMenu = $user->menus()->where('is_active', true)->first();
        if (!$activeMenu) {
            return response('OK', 200);
        }

        // Get Last Log
        $lastLog = \App\Models\WhatsappLog::where('user_id', $user->id)
            ->where('from_number', $from)
            ->latest()
            ->first();

        // Process Message (Bot Logic)
        $result = $this->processMessage($text, $activeMenu->tree, $lastLog);

        // Send Response
        $this->sendWhatsAppMessage($user, $from, $result['responseText']);

        // Log Interaction
        \App\Models\WhatsappLog::create([
            'user_id' => $user->id,
            'from_number' => $from,
            'message' => $text,
            'current_path' => $result['nextPath'],
            'response' => $result['responseText'],
            'was_ai' => false
        ]);

        // Upsert Lead
        \App\Models\Lead::updateOrCreate(
            ['user_id' => $user->id, 'phone' => $from],
            [
                'last_interaction' => now(),
                'current_path' => $result['nextPath'],
                'interactions_count' => \Illuminate\Support\Facades\DB::raw('interactions_count + 1')
            ]
        );

        return response('OK', 200);
    }

    private function processMessage($incomingText, $tree, $lastLog)
    {
        $text = trim($incomingText);
        
        // 1. Initial/Reset
        if (strtolower($text) === 'hola' || strtolower($text) === 'inicio' || !$lastLog) {
            return [
                'nextPath' => 'root',
                'responseText' => ($tree['welcome_message'] ?? '') . "\n\n" . $this->formatMenuOptions($tree['items']),
                'isAi' => false
            ];
        }

        // 2. Back (0)
        if ($text === '0') {
            return [
                'nextPath' => 'root',
                'responseText' => $this->formatMenuOptions($tree['items']),
                'isAi' => false
            ];
        }

        // 3. Find current node
        $currentNodeId = $lastLog->current_path ?? 'root';
        $currentOptions = $this->findNodesInPath($currentNodeId, $tree['items']);

        // 4. Find selected option
        $selectedNode = null;
        foreach ($currentOptions as $node) {
            if ($node['key'] === $text) {
                $selectedNode = $node;
                break;
            }
        }

        if ($selectedNode) {
            if (!empty($selectedNode['children'])) {
                return [
                    'nextPath' => $selectedNode['id'],
                    'responseText' => '*' . $selectedNode['label'] . "*\n\n" . $this->formatMenuOptions($selectedNode['children']),
                    'isAi' => false
                ];
            } else {
                return [
                    'nextPath' => 'root',
                    'responseText' => ($selectedNode['response'] ?? '') . "\n\n" . ($tree['goodbye_message'] ?? '¡Gracias!'),
                    'isAi' => false
                ];
            }
        }

        // 5. Invalid
        return [
            'nextPath' => $currentNodeId,
            'responseText' => "⚠️ Opción no válida. Por favor, elige un número válido o escribe 'inicio'.",
            'isAi' => false
        ];
    }

    private function formatMenuOptions($nodes)
    {
        if (empty($nodes)) return "No hay opciones.";
        $options = array_map(function($n) {
            return "*" . $n['key'] . ".* " . $n['label'];
        }, $nodes);
        return implode("\n", $options) . "\n\n*0.* Volver atrás";
    }

    private function findNodesInPath($pathId, $items)
    {
        if ($pathId === 'root') return $items;
        foreach ($items as $node) {
            if (($node['id'] ?? '') === $pathId) {
                return $node['children'] ?? [];
            }
            if (!empty($node['children'])) {
                $found = $this->findNodesInPath($pathId, $node['children']);
                if (!empty($found)) return $found;
            }
        }
        return [];
    }

    private function sendWhatsAppMessage($user, $to, $message)
    {
        $url = "https://graph.facebook.com/v18.0/{$user->whatsapp_phone_number_id}/messages";
        
        // In legacy, access token was decrypted.
        // Assuming plain text for now.
        $token = $user->whatsapp_access_token;

        \Illuminate\Support\Facades\Http::withToken($token)->post($url, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $message]
        ]);
    }
}
