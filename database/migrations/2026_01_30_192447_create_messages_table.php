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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            
            // Dirección: inbound (usuario -> bot) / outbound (bot -> usuario)
            $table->enum('direction', ['inbound', 'outbound']);
            
            // Tipo de mensaje: text, interactive, image, etc.
            $table->string('type');
            
            // Contenido del mensaje (texto o payload JSON)
            $table->text('body')->nullable();
            
            // ID del mensaje en WhatsApp (wamid)
            $table->string('whatsapp_message_id')->nullable()->index();
            
            // Estado del mensaje (sent, delivered, read)
            $table->string('status')->default('sent');
            
            $table->json('payload')->nullable(); // Payload completo raw
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
