<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class AIService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function sendPrompt(string $prompt): array
    {
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.config('services.openrouter.key'),
            'Content-Type' => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'qwen/qwen3-coder:free', 
            'messages' => [
                ['role' => 'system', 'content' => 'You are a quiz generator AI.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);
        
        if ($response->failed()) {
            throw new Exception('AI API request failed');
        }

        return $response->json();
    }
}
