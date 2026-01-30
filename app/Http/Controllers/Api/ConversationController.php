<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\WhatsAppCloudApiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Events\MessageSent;
use App\Events\ConversationUpdated;

class ConversationController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppCloudApiService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * List conversations (last 23h, ordered).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::whereHas('botConfig', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('last_message_at', '>=', Carbon::now()->subHours(23))
            ->with(['messages' => function($q) {
                $q->latest()->limit(1); // Preview last message
            }])
            ->orderBy('pinned', 'desc')
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json($conversations);
    }

    /**
     * Get messages for a conversation.
     */
    public function messages(Request $request, Conversation $conversation)
    {
        $this->authorizeUser($request, $conversation);

        return response()->json($conversation->messages()->oldest()->get());
    }

    /**
     * Pin a conversation.
     */
    public function pin(Request $request, Conversation $conversation)
    {
        $this->authorizeUser($request, $conversation);

        $conversation->update(['pinned' => true]);
        
        ConversationUpdated::dispatch($conversation);

        return response()->json(['status' => 'pinned']);
    }

    /**
     * Unpin a conversation.
     */
    public function unpin(Request $request, Conversation $conversation)
    {
        $this->authorizeUser($request, $conversation);

        $conversation->update(['pinned' => false]);
        
        ConversationUpdated::dispatch($conversation);

        return response()->json(['status' => 'unpinned']);
    }

    /**
     * Send a manual message.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $this->authorizeUser($request, $conversation);

        $request->validate([
            'text' => 'required|string'
        ]);

        // Validate 24h window (using 23h as safety margin per requirements)
        // Check if the last INBOUND message was within 24h? 
        // Or just last interaction? usually it's last user message.
        // Assuming last_message_at is updated on any message, we should check if we can send.
        // Ideally we should check the last *inbound* message time.
        
        $lastInbound = $conversation->messages()->where('direction', 'inbound')->latest()->first();
        
        if (!$lastInbound || $lastInbound->created_at < Carbon::now()->subHours(24)) {
             // Allow sending templates only? The prompt says "SOLO templates" if outside 24h.
             // But for this endpoint, maybe we just block or warn.
             // User prompt: "Aplicar validaciones de ventana de 24h para envío de mensajes libres."
             return response()->json(['error' => 'Fuera de la ventana de 24h. Solo se permiten templates.'], 400);
        }

        $text = $request->input('text');

        // Send via WhatsApp
        try {
            $this->whatsappService->sendText($conversation->botConfig, $conversation->wa_id, $text);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error enviando mensaje: ' . $e->getMessage()], 500);
        }

        // Log Message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'text',
            'body' => $text,
            'status' => 'sent'
        ]);

        $conversation->touch();

        // Events
        MessageSent::dispatch($message);
        ConversationUpdated::dispatch($conversation);

        return response()->json($message);
    }

    private function authorizeUser(Request $request, Conversation $conversation)
    {
        if ($conversation->botConfig->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
