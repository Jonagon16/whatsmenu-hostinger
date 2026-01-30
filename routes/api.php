<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard/stats', [\App\Http\Controllers\Api\DashboardController::class, 'stats']);
    Route::get('/dashboard/chart-data', [\App\Http\Controllers\Api\DashboardController::class, 'chartData']);
    Route::get('/dashboard/activity', [\App\Http\Controllers\Api\DashboardController::class, 'activity']);

    // Dashboard Conversations
    Route::get('/dashboard/conversations', [\App\Http\Controllers\Api\ConversationController::class, 'index']);
    Route::post('/conversations/{conversation}/pin', [\App\Http\Controllers\Api\ConversationController::class, 'pin']);
    Route::post('/conversations/{conversation}/unpin', [\App\Http\Controllers\Api\ConversationController::class, 'unpin']);
    Route::get('/conversations/{conversation}/messages', [\App\Http\Controllers\Api\ConversationController::class, 'messages']);
    Route::post('/conversations/{conversation}/send-message', [\App\Http\Controllers\Api\ConversationController::class, 'sendMessage']);
});

// WhatsApp Webhook
Route::get('/webhooks/whatsapp', [\App\Http\Controllers\WhatsAppWebhookController::class, 'verify']);
Route::post('/webhooks/whatsapp', [\App\Http\Controllers\WhatsAppWebhookController::class, 'receive']);
