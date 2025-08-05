<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\AuthController;

// Welcome page route
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Chatbot routes
Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot');
Route::post('/chatbot/send', [ChatbotController::class, 'send'])->name('chatbot.send');

// API route for chatbot (no middleware)
Route::post('/api/chatbot/send', [ChatbotController::class, 'send'])->name('api.chatbot.send')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Protected Admin routes
Route::middleware(['admin'])->group(function () {
    Route::resource('knowledge-base', KnowledgeBaseController::class);
});

// Admin dashboard route (redirects to login if not authenticated)
Route::get('/admin', function () {
    if (auth()->check()) {
        return redirect()->route('knowledge-base.index');
    }
    return redirect()->route('auth.login');
})->name('admin');

