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
        Schema::create('bot_config', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();

            // UUID compatible con users.id (CHAR(36))
            $table->char('user_id', 36);

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->text('whatsapp_verify_token');
            $table->text('whatsapp_access_token');
            $table->text('whatsapp_phone_number_id');
            $table->text('whatsapp_business_account_id')->nullable();

            $table->boolean('is_active')->default(true);
            $table->text('bot_name')->default('Mi Bot');

            $table->timestampTz('last_interaction_at')->nullable();

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_config');
    }
};
