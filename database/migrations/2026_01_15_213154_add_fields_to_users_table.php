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
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_key')->nullable()->unique();
            $table->enum('plan', ['free', 'plus', 'pro'])->default('free');
            $table->string('whatsapp_phone_number_id')->nullable();
            $table->string('whatsapp_access_token')->nullable();
            $table->string('whatsapp_business_id')->nullable();
            $table->boolean('ai_enabled')->default(false);
            $table->text('ai_instruction')->nullable();
            // Billing fields
            $table->string('billing_document_type')->nullable();
            $table->string('billing_document_number')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_zip')->nullable();
            $table->string('billing_country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'api_key', 'plan', 'whatsapp_phone_number_id', 'whatsapp_access_token', 
                'whatsapp_business_id', 'ai_enabled', 'ai_instruction',
                'billing_document_type', 'billing_document_number', 'billing_address',
                'billing_city', 'billing_state', 'billing_zip', 'billing_country'
            ]);
        });
    }
};
