<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionPool;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use App\Services\QuestionGeneratorService;
use Illuminate\Http\Response;

class QuestionPoolAIController extends Controller
{
    public function showAIGenerator(QuestionPool $questionPool)
    {
        $organization = auth()->user()->organizations()->first();

        if (!$organization->canGenerateAiQuestion()) {
            $canGenAiQue = false;
        }else{
            $canGenAiQue = true;
        }
        
        return Inertia::render('Examiner/QuestionPools/AIGenerator', [
            'questionPool' => $questionPool->only(['id', 'name']),
            'canGenAiQue' => $canGenAiQue,
        ]);
    }

    public function generate(Request $request, QuestionGeneratorService $questionService)
    {
        

        $validated = $request->validate([
            'source_type' => 'required|in:topic,article',
            'topic' => 'required_if:source_type,topic|string|max:255',
            'article' => 'nullable|string|max:5000',
            'question_type' => 'required|in:random,multiple_choice,true_false,short_answer,fill_in_the_blank,matching,ordering',
            'difficulty' => 'required|in:easy,medium,hard',
            'number_of_questions' => 'required|integer|min:1|max:20',
            'language' => 'required|string|max:50',
            'pool_id' => 'required|exists:question_pools,id',
        ]);
    
        try {
            $questions = $questionService->generate($validated);
    
            if (!is_array($questions)) {
                throw new \Exception('AI response format invalid');
            }
    
            $validQuestions = array_map(function ($question) {
                if (!isset($question['question'], $question['type'])) {
                    throw new \Exception('AI returned incomplete question format');
                }
                return [
                    'question' => $question['question'],
                    'type' => $question['type'],
                    'options' => $question['options'] ?? [],
                    'correct_answers' => $question['correct_answers'] ?? [],
                    'explanation' => $question['explanation'] ?? null,
                ];
            }, $questions);
    
            return response()->json(['questions' => $validQuestions]);
    
        } catch (\Exception $e) {
            \Log::error('AI Question Generation Failed: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $organization = auth()->user()->organizations()->first();

        if (!$organization->canGenerateAiQuestion() && !$organization->canAddQuestion()) {
            redirect()->back()->with('success', 'You have reached either your Ai or Question limits, kindly check your dashboard page.');
        }


        $validated = $request->validate([
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string|max:1000',
            'questions.*.type' => 'required',
            'questions.*.options' => 'required_if:questions.*.type,multiple_choice|array',
            'questions.*.correct_answers' => 'nullable|array',
            'pool_id' => 'required|exists:question_pools,id'
        ]);

        $pool = QuestionPool::findOrFail($request->pool_id);
        

        foreach ($request->questions as $questionData) {
            $question = new Question([
                'question' => $questionData['question'],
                'type' => $questionData['type'],
                'options' => $questionData['options'] ?? null,
                'correct_answers' => $questionData['correct_answers'] ?? null,
                'points' => 1, 
                'is_required' => true,
                'organization_id' => $organization->id,
                'is_ai' => 1,
            ]);

            $question->save();
            $pool->questions()->attach($question->id);
        }

        return redirect()->back()->with('success', 'Ai Questions added to pool successfully');
    }

    private function buildPrompt(Request $request): string
    {
        $instructions = [
            "Generate {$request->number_of_questions} quiz questions",
            "Question type: {$request->question_type}",
            "Difficulty: {$request->difficulty}",
            "Language: {$request->language}",
            "Format: Return a valid JSON array where each question has: question (string), type (string), options (array if applicable), correct_answers (array of indices or values)",
        ];

        if ($request->source_type === 'topic') {
            $instructions[] = "Topic: {$request->topic}";
        } else {
            $instructions[] = "Based on this article: {$request->article}";
        }

        $examples = [
            'multiple_choice' => [
                'question' => 'What is the capital of France?',
                'type' => 'multiple_choice',
                'options' => ['London', 'Paris', 'Berlin', 'Madrid'],
                'correct_answers' => [1]
            ],
            'true_false' => [
                'question' => 'The Earth is flat.',
                'type' => 'true_false',
                'correct_answers' => [false]
            ],
            'short_answer' => [
                'question' => 'Who wrote "Romeo and Juliet"?',
                'type' => 'short_answer',
                'correct_answers' => ['William Shakespeare']
            ]
        ];

        return implode("\n", $instructions) . "\n\nExample formats:\n" . json_encode($examples, JSON_PRETTY_PRINT);
    }

    private function callOpenRouterAPI(string $prompt): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openrouter.key'),
            'HTTP-Referer' => config('app.url'),
            'X-Title' => config('app.name'),
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'openai/gpt-3.5-turbo', // or any other model you prefer
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'response_format' => ['type' => 'json_object'],
        ]);

        return $response->json();
    }
}
