<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gemini;

class ChatbotController extends Controller
{
    public function geminiChat(Request $request)
    {
        $message = $request->input('message', $request->query('message'));
        $type = $request->input('type', $request->query('type', 'text'));
        if (!$message) {
            return response()->json(['error' => 'Missing message parameter.'], 400);
        }
        $gemini = new Gemini();
        $response = $gemini->chat($message, $type);
        return response()->json($response);
    }

    public function openaiChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'type' => 'required|in:o1-mini,dall-e-2',
        ]);
        $openai = new \App\Models\OpenAI();
        $response = $openai->chat($request->input('message'), $request->input('type'));
        return response()->json($response);
    }
} 