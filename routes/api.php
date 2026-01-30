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
});
