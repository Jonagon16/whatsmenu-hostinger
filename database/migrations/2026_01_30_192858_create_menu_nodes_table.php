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
        Schema::create('menu_nodes', function (Blueprint $table) {
            $table->id();
            
            // Relación con el Menú (que pertenece a un BotConfig/Tenant)
            // Asumimos que la tabla 'menus' ya existe y tiene un user_id o bot_config_id
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            
            // Identificador único del nodo dentro del menú (ej: 'root', 'horarios', 'productos')
            $table->string('slug')->index(); 
            
            // Título/Header del mensaje
            $table->string('title')->nullable();
            
            // Cuerpo del mensaje
            $table->text('body_text');
            
            // Footer opcional
            $table->string('footer_text')->nullable();
            
            // Tipo de nodo: list, buttons, text, product, etc.
            $table->enum('type', ['text', 'buttons', 'list', 'product'])->default('text');
            
            $table->timestamps();

            // Un slug no se puede repetir dentro del mismo menú
            $table->unique(['menu_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_nodes');
    }
};
