<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;

Route::get('/aaa', function () {
    return view('welcome');
});


Route::get('/', [ChatbotController::class, 'index']);
Route::post('/chatbot/send', [ChatbotController::class, 'send']);

