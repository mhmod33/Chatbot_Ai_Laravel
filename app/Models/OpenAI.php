<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;

class OpenAI
{
    protected $apiKey;
    protected $textEndpoint = 'https://openrouter.ai/api/v1/chat/completions';
    protected $imageEndpoint = 'https://api.openai.com/v1/images/generations';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    public function chat($message, $type = 'openai/gpt-4o')
    {
        if ($type === 'dall-e-2') {
            // Image generation
            $response = Http::withToken($this->apiKey)
                ->post($this->imageEndpoint, [
                    'prompt' => $message,
                    'n' => 1,
                    'size' => '512x512',
                    'model' => 'dall-e-2',
                ]);
            if ($response->successful() && isset($response['data'][0]['url'])) {
                return ['image_url' => $response['data'][0]['url']];
            }
            $details = null;
            try {
                $details = $response->json();
            } catch (\Throwable $e) {
                $details = $response->body();
            }
            return [
                'error' => 'Failed to get image from DALL-E-2',
                'status' => $response->status(),
                'details' => $details,
            ];
        } else {
            // Text generation (openai/gpt-4o or similar)
            $payload = [
                'model' => $type,
                'messages' => [
                    ['role' => 'user', 'content' => $message]
                ],
                'max_tokens' => 1000,
            ];
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => url('/'), // Optional: for OpenRouter analytics
                'X-Title' => 'Laravel AI Reminder Agent', // Optional: for OpenRouter analytics
            ])->post($this->textEndpoint, $payload);
            if ($response->successful() && isset($response['choices'][0]['message']['content'])) {
                return ['text' => $response['choices'][0]['message']['content']];
            }
            $details = null;
            try {
                $details = $response->json();
            } catch (\Throwable $e) {
                $details = $response->body();
            }
            return [
                'error' => 'Failed to get response from OpenRouter',
                'status' => $response->status(),
                'details' => $details,
            ];
        }
    }
} 