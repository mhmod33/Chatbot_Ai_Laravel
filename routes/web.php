<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\RagController;

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/chat/gemini', [ChatbotController::class, 'geminiChat']);
Route::view('/gemini-chat', 'gemini-chat');
Route::view('/openai-chat', 'openai-chat');
Route::post('/chat/openai', [ChatbotController::class, 'openaiChat']);
Route::post('/reminders', [ReminderController::class, 'create']);
Route::view('/reminder', 'reminder');
Route::get('/rag', [RagController::class, 'index']);
Route::post('/rag/query', [RagController::class, 'query']);
