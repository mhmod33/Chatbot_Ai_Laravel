<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Knowledge;
use App\Models\OpenAI;

class RagController extends Controller
{
    public function index()
    {
        return view('rag');
    }

    public function query(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
        ]);
        $question = $request->input('question');
        // Simple retrieval: find the most relevant knowledge entry by keyword match
        $knowledge = Knowledge::where('content', 'like', '%' . $question . '%')
            ->orWhere('title', 'like', '%' . $question . '%')
            ->orderBy('id', 'desc')
            ->first();
        $context = $knowledge ? ($knowledge->title . ":\n" . $knowledge->content) : 'No relevant knowledge found.';
        $openai = new OpenAI();
        $prompt = "You are an assistant. Use the following knowledge to answer the user's question.\nKnowledge:\n" . $context . "\nQuestion: " . $question . "\nAnswer:";
        $response = $openai->chat($prompt, 'openai/gpt-4o');
        $answer = $response['text'] ?? 'Sorry, I could not generate an answer.';
        return response()->json([
            'answer' => $answer,
            'knowledge' => $knowledge ? $knowledge->only(['title', 'content']) : null,
        ]);
    }
}
