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
        $response = $this->aiService->sendPrompt($prompt, $data['number_of_questions']);
    
        if (!isset($response['choices'][0]['message']['content'])) {
            throw new Exception('Invalid response from AI service - missing content');
        }
    
        $content = $this->cleanJsonResponse($response['choices'][0]['message']['content']);
        $result = $this->parseJsonResponse($content);
        
        $this->validateQuestions($result, $data['number_of_questions']);
        
        return $result;
    }

    private function cleanJsonResponse(string $content): string
    {
        // Remove JSON markdown code blocks if present
        $content = preg_replace('/^```json|```$/m', '', $content);
        $content = trim($content);
        
        // Remove any non-JSON prefix/suffix text
        if (preg_match('/\{.*\}|\[.*\]/s', $content, $matches)) {
            $content = $matches[0];
        }
        
        return $content;
    }

    private function parseJsonResponse(string $content): array
    {
        $result = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to parse AI response: '.json_last_error_msg().' Content: '.substr($content, 0, 100));
        }
        
        return $result;
    }

    private function validateQuestions(array $result, int $expectedCount): void
    {
        // Handle different response formats
        if (isset($result['questions'])) {
            $questions = $result['questions'];
        } elseif (is_array($result) && isset($result[0]['question'])) {
            $questions = $result;
        } else {
            throw new Exception('Unexpected response format from AI - missing questions array');
        }
        
        // Validate count
        if (count($questions) !== $expectedCount) {
            throw new Exception(sprintf(
                'AI returned %d questions but expected %d',
                count($questions),
                $expectedCount
            ));
        }
        
        // Validate each question structure
        foreach ($questions as $question) {
            if (!isset($question['question'], $question['type'])) {
                throw new Exception('Invalid question format - missing required fields');
            }
            
            // Validate question types
            $validTypes = [
                'multiple_choice', 'true_false', 'short_answer', 
                'matching', 'ordering', 'fill_in_the_blank'
            ];
            
            if (!in_array($question['type'], $validTypes)) {
                throw new Exception('Invalid question type: '.$question['type']);
            }
        }
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
Generate exactly {$data['number_of_questions']} {$data['difficulty']} level {$questionType} questions in {$data['language']}.
You MUST return exactly {$data['number_of_questions']} questions.

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
    }
]
PROMPT;
    }
}