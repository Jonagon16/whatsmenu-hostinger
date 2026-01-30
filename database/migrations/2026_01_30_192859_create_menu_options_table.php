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
        Schema::create('menu_options', function (Blueprint $table) {
            $table->id();
            
            // Nodo al que pertenece esta opción
            $table->foreignId('menu_node_id')->constrained()->onDelete('cascade');
            
            // Texto que ve el usuario (ej: "Ver Productos")
            $table->string('label');
            
            // Descripción (opcional, para listas)
            $table->string('description')->nullable();
            
            // ID del nodo al que lleva esta opción (ej: 'productos')
            // No usamos foreign key estricta a 'menu_nodes.id' para permitir autoreferencias o slugs, 
            // pero idealmente apuntaría al slug o ID del siguiente nodo.
            $table->string('next_node_slug')->nullable();
            
            // Acción especial (opcional): link, call, location, etc.
            $table->string('action_type')->nullable(); // 'link', 'call', 'node'
            $table->string('action_value')->nullable(); // 'https://google.com', '+549...', etc.
            
            // Orden de visualización
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_options');
    }
};
