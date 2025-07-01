<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;

class OpenAI
{
    protected $apiKey;
    protected $textEndpoint = 'https://api.openai.com/v1/chat/completions';
    protected $imageEndpoint = 'https://api.openai.com/v1/images/generations';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    public function chat($message, $type = 'o1-mini')
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
            // Text generation (o1-mini or similar)
            $payload = [
                'model' => 'o1-mini',
                'messages' => [
                    ['role' => 'user', 'content' => $message]
                ]
            ];
            $response = Http::withToken($this->apiKey)
                ->post($this->textEndpoint, $payload);
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
                'error' => 'Failed to get response from OpenAI',
                'status' => $response->status(),
                'details' => $details,
            ];
        }
    }
} 