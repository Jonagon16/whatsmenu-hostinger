<?php

namespace App\Jobs;

use App\Models\BotConfig;
use App\Services\IncomingMessageHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $botConfig;
    protected $messageData;

    /**
     * Create a new job instance.
     */
    public function __construct(BotConfig $botConfig, array $messageData)
    {
        $this->botConfig = $botConfig;
        $this->messageData = $messageData;
    }

    /**
     * Execute the job.
     */
    public function handle(IncomingMessageHandler $handler): void
    {
        try {
            $handler->handle($this->botConfig, $this->messageData);
        } catch (\Exception $e) {
            Log::error('Fallo procesando Job de WhatsApp', [
                'error' => $e->getMessage(),
                'tenant_id' => $this->botConfig->id,
                'message_data' => $this->messageData
            ]);
            
            // Opcional: Reintentar o fallar
            $this->fail($e);
        }
    }
}
