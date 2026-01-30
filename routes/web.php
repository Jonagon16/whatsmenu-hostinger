<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

Route::get('/benefits', function () {
    return view('benefits');
})->name('benefits');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

// Dashboard route moved to controller

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('menus', \App\Http\Controllers\MenuController::class);
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});

// Rutas de Prueba (Temporales)
Route::prefix('test-panel')->group(function () {
    Route::get('/', [\App\Http\Controllers\TestController::class, 'index'])->name('tests.jona');
    Route::post('/reset', [\App\Http\Controllers\TestController::class, 'reset'])->name('tests.reset');
    Route::post('/simulate', [\App\Http\Controllers\TestController::class, 'simulateWebhook'])->name('tests.simulate');
});

