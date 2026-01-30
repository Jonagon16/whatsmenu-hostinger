<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            
            // Relación con el Tenant (BotConfig)
            $table->foreignId('bot_config_id')->constrained('bot_config')->onDelete('cascade');
            
            // ID del usuario de WhatsApp (número)
            $table->string('wa_id')->index();
            
            // Estado actual de la conversación (para flujos)
            $table->string('current_node_id')->default('root');
            
            // Metadata extra (nombre, perfil, variables de sesión)
            $table->json('metadata')->nullable();
            
            $table->timestamp('last_message_at')->useCurrent();
            $table->timestamps();

            // Un usuario solo puede tener una conversación activa por bot
            $table->unique(['bot_config_id', 'wa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
