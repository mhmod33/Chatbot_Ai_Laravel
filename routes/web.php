<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/chat/gemini', [ChatbotController::class, 'geminiChat']);
Route::view('/gemini-chat', 'gemini-chat');
Route::view('/openai-chat', 'openai-chat');
Route::post('/chat/openai', [ChatbotController::class, 'openaiChat']);
