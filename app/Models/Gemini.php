<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;

class Gemini
{
    protected $apiKey;
    protected $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function chat($message, $type = 'text')
    {
        if ($type === 'image') {
            // Gemini API v1beta does not support image generation directly.
            // For demonstration, return a placeholder image URL based on the message.
            // In a real implementation, you would call an image generation API here.
            return [
                'image_url' => 'https://via.placeholder.com/400x200.png?text=' . urlencode($message)
            ];
        }
        $response = Http::post($this->endpoint . '?key=' . $this->apiKey, [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $message
                        ]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            return $response->json();
        }
        return [
            'error' => 'Failed to get response from Gemini',
            'details' => $response->body(),
        ];
    }
} 