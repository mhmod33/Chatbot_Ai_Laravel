<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gemini;
use App\Models\Reminder;
use Illuminate\Support\Facades\Auth;
use App\Models\OpenAI;

class ReminderController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'email' => 'nullable|email',
        ]);
        $user = Auth::user();
        $email = $user ? $user->email : $request->input('email');
        if (!$email) {
            return response()->json(['error' => 'Email is required if not authenticated.'], 422);
        }
        $openai = new OpenAI();
        $prompt = "Extract the task and datetime from this reminder request. Respond in JSON with 'task' and 'remind_at' (ISO 8601 format). Message: " . $request->input('message');
        $response = $openai->chat($prompt, 'openai/gpt-4o');
        $data = $response['text'] ?? null;
        if (!$data) {
            return response()->json(['error' => 'Could not parse reminder.', 'details' => $response], 422);
        }
        $data = preg_replace('/^```json|^```/m', '', $data);
        $data = preg_replace('/```$/m', '', $data);
        $data = trim($data);
        $parsed = json_decode($data, true);
        if (!$parsed || !isset($parsed['task'], $parsed['remind_at'])) {
            return response()->json(['error' => 'Invalid AI response.', 'details' => $data], 422);
        }
        $reminder = Reminder::create([
            'user_id' => $user ? $user->id : null,
            'email' => $email,
            'task' => $parsed['task'],
            'remind_at' => $parsed['remind_at'],
            'sent' => false,
        ]);
        // Send the email immediately
        \Mail::to($reminder->email)->send(new \App\Mail\ReminderMail($reminder));
        $reminder->sent = true;
        $reminder->save();
        return response()->json(['success' => true, 'reminder' => $reminder]);
    }
}
