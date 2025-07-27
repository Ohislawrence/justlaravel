<?php

namespace App\Services;

use Exception;
use App\Services\AIService;

class QuestionGeneratorService
{
    

    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generate(array $data): array
    {
        $prompt = $this->buildPrompt($data);
        $response = $this->aiService->sendPrompt($prompt);
    
        if (!isset($response['choices'][0]['message']['content'])) {
            throw new Exception('Invalid response from AI service');
        }
    
        $content = trim($response['choices'][0]['message']['content']);
        
        // Clean the response
        $content = preg_replace('/^```json|```$/m', '', $content);
        $content = trim($content);
    
        $result = json_decode($content, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to parse AI response: '.json_last_error_msg());
        }
    
        // Handle different response formats
        if (isset($result['questions'])) {
            return $result['questions'];
        } elseif (is_array($result) && isset($result[0]['question'])) {
            return $result; // Assume it's already the questions array
        }
    
        throw new Exception('Unexpected response format from AI');
    }

    private function buildPrompt(array $data): string
    {
        $questionType = $data['question_type'] === 'random'
            ? 'multiple_choice, matching, true_false, short_answer, ordering, fill_in_the_blank'
            : $data['question_type'];
    
        $source = $data['source_type'] === 'topic'
            ? "Topic: {$data['topic']}"
            : "Article: {$data['article']}";
    
        return <<<PROMPT
    Generate {$data['number_of_questions']} {$data['difficulty']} level {$questionType} questions in {$data['language']}.
    
    Source:
    {$source}
    
    Return ONLY a **valid JSON array** (no explanation text, no Markdown, no backticks).
    Each element must have:
    - question: string
    - type: string (one of: multiple_choice, true_false, short_answer, matching, ordering, fill_in_the_blank)
    - options: array (if applicable, else empty array)
    - correct_answers: array (strings or indexes depending on type)
    - explanation: optional string
    
    Example output:
    [
        {
            "question": "What is the capital of France?",
            "type": "multiple_choice",
            "options": ["London", "Paris", "Berlin", "Madrid"],
            "correct_answers": ["Paris"],
            "explanation": "Paris has been the capital of France since 508 AD"
        },
        {
            "question": "The Earth is flat.",
            "type": "true_false",
            "correct_answers": ["false"],
            "explanation": "The Earth is an oblate spheroid."
        },
        {
            "question": "The boy is [blank] school.",
            "type": "fill_in_the_blank",
            "options": [],
            "correct_answers": ["going"],
            "explanation": "Fill in the correct word"
        }
    ]
    PROMPT;
    }
}


