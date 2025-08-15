<?php

namespace App\Http\Controllers\Examinee;

use App\Http\Controllers\Controller;
use App\Models\QuestionResponse;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Services\ActivityService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuizAttemptController extends Controller
{
    public function start(Quiz $quiz, Request $request)
    {
        // Validate attempt limit
        if ($quiz->max_attempts) {
            $attempts = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('user_id', $request->user()->id)
                ->count();
                
            if ($attempts >= $quiz->max_attempts) {
                return redirect()->back()->with('error', 'You have reached the maximum number of attempts');
            }
        }

        // Prepare questions FIRST
        $questions = $this->prepareQuestions($quiz);

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $request->user()->id,
            'attempt_number' => $this->getNextAttemptNumber($quiz, $request->user()->id),
            'started_at' => now(),
            'status' => 'in_progress',
            'questions_data' => $questions 
        ]);

        // Prepare questions
        //$questions = $this->prepareQuestions($quiz);
        
        // Return Inertia response
        // Redirect to the attempt page
        return redirect()->route('examinee.attempt', [
            'quiz' => $quiz->id,
            'attempt' => $attempt->id
        ]);
    }

    public function showAttempt(Quiz $quiz, Request $request)
    {
        // Get the latest in-progress attempt or 404
        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $request->user()->id)
            ->whereNull('completed_at')
            ->latest()
            ->firstOrFail();
        
        // Questions are already stored from start()
        $questions = $attempt->questions_data;

        return Inertia::render('Examinee/QuizPlayer', [
            'quiz' => $quiz->only('id', 'title', 'time_limit'),
            'attempt' => $attempt,
            'questions' => $questions,
            'timeLimit' => $quiz->time_limit
        ]);
    }

    protected function getNextAttemptNumber(Quiz $quiz, $userId)
    {
        return QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $userId)
            ->count() + 1;
    }

    protected function prepareQuestions(Quiz $quiz)
    {
        $questions = collect();
    
        // 1. Add direct questions
        $questions = $questions->merge(
            $quiz->questions()
                ->whereDoesntHave('pools', fn($q) => $q->where('quiz_id', $quiz->id))
                ->get()
                ->map(fn($q) => $this->formatQuestion($q))
        );
    
        // 2. Add pool questions
        foreach ($quiz->questionPools as $pool) {
            $poolQuestions = $pool->questions()
                ->inRandomOrder()
                ->take($pool->pivot->questions_to_show)
                ->get()
                ->map(fn($q) => $this->formatQuestion($q));
                
            // Store the pool ID with each question
            $poolQuestions->each(function($q) use ($pool) {
                $q['pool_id'] = $pool->id;
            });
            
            $questions = $questions->merge($poolQuestions);
        }
    
        // Shuffle if needed and convert to array
        return $questions->shuffle($quiz->randomize_questions ? null : false)
                        ->values() // Reset keys
                        ->toArray();
    }

    protected function formatQuestion($question)
    {
        // Ensure options and correct_answers are properly formatted
        $options = $question->options;
        $correctAnswers = $question->correct_answers;
        
        if (is_string($options)) {
            $options = json_decode($options, true) ?? [];
        }
        
        if (is_string($correctAnswers)) {
            $correctAnswers = json_decode($correctAnswers, true) ?? [];
        }
    
        return [
            'id' => $question->id,
            'pool_id' => $question->pivot->question_pool_id ?? null, // Track pool questions
            'type' => $question->type,
            'question' => $question->question,
            'options' => $options,
            'correct_answers' => $correctAnswers,
            'points' => $question->points,
            'time_limit' => $question->time_limit,
            'settings' => array_merge([
                'show_feedback' => false,
                'randomize_options' => false
            ], $question->settings ?? []),
            'is_required' => $question->is_required ?? true
        ];
    }


    protected function getAttemptQuestions(QuizAttempt $attempt)
    {
        $questionsData = $attempt->questions_data;
    
        // If it's already an array, return it directly
        if (is_array($questionsData)) {
            return $questionsData;
        }
    
        // If it's a JSON string, decode it
        if (is_string($questionsData)) {
            try {
                $decoded = json_decode($questionsData, true);
                return is_array($decoded) ? $decoded : [];
            } catch (\Exception $e) {
                Log::error('Failed to decode questions_data: ' . $e->getMessage());
                return [];
            }
        }
    
        // Fallback for any other case
        return [];
    }

    
    
    protected function normalizeCorrectAnswers($correctAnswers)
    {
        if (is_string($correctAnswers)) {
            $decoded = json_decode($correctAnswers, true);
            return $decoded !== null ? $decoded : [$correctAnswers];
        }
        
        if (!is_array($correctAnswers)) {
            return [$correctAnswers];
        }
        
        return $correctAnswers;
    }

    protected function gradeQuestion($question, $userAnswer, array $correctAnswers): bool
    {
        // Ensure we have the required fields
        $type = is_array($question) ? ($question['type'] ?? null) : $question->type;
        $settings = is_array($question) ? ($question['settings'] ?? []) : ($question->settings ?? []);

        if (!$type) {
            throw new \InvalidArgumentException('Question type is required');
        }

        // Skip null or empty answers
        if ($userAnswer === null || $userAnswer === '') {
            return false;
        }

        // Handle JSON answers
        if (is_string($userAnswer) && $this->isJsonString($userAnswer)) {
            $userAnswer = json_decode($userAnswer, true);
        }

        switch ($type) {
            case 'multiple_choice':
                return $this->gradeMultipleChoiceQuestion($userAnswer, $correctAnswers);
            case 'true_false':
                return $this->gradeTrueFalseQuestion($userAnswer, $correctAnswers);
            case 'fill_in_the_blank':
                return $this->gradeFillInTheBlank($userAnswer, $correctAnswers, $settings);
            case 'short_answer':
                return $this->gradeShortAnswer($userAnswer, $correctAnswers, $settings);
            case 'ordering':
                return $this->gradeOrdering($userAnswer, $correctAnswers);
            case 'matching':
                $options = is_array($question) ? ($question['options'] ?? []) : $question->options;
                return $this->gradeMatchingQuestion($userAnswer, $correctAnswers, $options);
            default:
                return false;
        }
    }

    protected function gradeMultipleChoiceQuestion($userAnswer, array $correctAnswers): bool
    {
        // Handle case where user didn't answer
        if ($userAnswer === null || $userAnswer === '') {
            return false;
        }
    
        // Convert both user answer and correct answers to strings for comparison
        $userAnswer = is_array($userAnswer) ? $userAnswer[0] ?? '' : $userAnswer;
        $userAnswer = (string)$userAnswer;
        
        // Compare with each correct answer (case-sensitive)
        foreach ($correctAnswers as $correctAnswer) {
            if ((string)$correctAnswer === $userAnswer) {
                return true;
            }
        }
        
        return false;
    }
    
    protected function gradeTrueFalseQuestion($userAnswer, array $correctAnswers): bool
    {
        // Get the first correct answer (should be "true" or "false")
        $correctAnswer = $correctAnswers[0] ?? null;
        
        // Convert user answer to string and lowercase
        $userAnswer = is_array($userAnswer) ? $userAnswer[0] ?? '' : $userAnswer;
        $userAnswer = strtolower((string)$userAnswer);
        
        // Convert correct answer to string and lowercase
        $correctAnswer = strtolower((string)$correctAnswer);
        
        // Special cases for boolean strings
        if ($userAnswer === '1') $userAnswer = 'true';
        if ($userAnswer === '0') $userAnswer = 'false';
        
        //return $userAnswer === $correctAnswer;
        return strtolower((string)$userAnswer) === $correctAnswer;
    }

    private function gradeFillInTheBlank($userAnswer, array $correctAnswers, array $settings): bool
    {
        // Ensure userAnswer is an array
        $userAnswers = is_array($userAnswer) ? $userAnswer : [$userAnswer];
        
        // Check if number of answers matches expected blanks
        if (count($userAnswers) !== count($correctAnswers)) {
            return false;
        }
        
        // Check each blank answer
        foreach ($userAnswers as $index => $answer) {
            $correctAnswer = $correctAnswers[$index] ?? '';
            
            // Get case sensitivity setting for this blank
            $caseSensitive = $settings['case_sensitive'][$index] ?? false;
            
            // Clean and compare answers
            $userAnswerClean = trim($answer ?? '');
            $correctAnswerClean = trim($correctAnswer ?? '');
            
            if ($caseSensitive) {
                if ($userAnswerClean !== $correctAnswerClean) {
                    return false;
                }
            } else {
                if (strtolower($userAnswerClean) !== strtolower($correctAnswerClean)) {
                    return false;
                }
            }
        }
        
        return true;
    }

    private function gradeShortAnswer($userAnswer, array $correctAnswers, array $settings): bool
    {
        // Handle null/empty cases first
        if ($userAnswer === null || $userAnswer === '') {
            return false;
        }
    
        // Normalize user answer
        if (is_array($userAnswer)) {
            $userAnswer = $userAnswer[0] ?? '';
        }
        $userAnswer = trim((string)$userAnswer);
    
        // Handle empty correct answers
        if (empty($correctAnswers)) {
            return false;
        }
    
        // Normalize correct answers
        $correctAnswers = array_filter(array_map(function($answer) {
            return trim((string)$answer);
        }, $correctAnswers));
    
        // Check case sensitivity setting
        $caseSensitive = $settings['case_sensitive'] ?? false;
        $trimWhitespace = $settings['trim_whitespace'] ?? true;
        $ignorePunctuation = $settings['ignore_punctuation'] ?? false;
    
        // Normalize further based on settings
        if ($trimWhitespace) {
            $userAnswer = preg_replace('/\s+/', ' ', $userAnswer);
        }
    
        if ($ignorePunctuation) {
            $userAnswer = preg_replace('/[^\w\s]/u', '', $userAnswer);
            $correctAnswers = array_map(function($answer) {
                return preg_replace('/[^\w\s]/u', '', $answer);
            }, $correctAnswers);
        }
    
        // Perform comparison
        if ($caseSensitive) {
            return in_array($userAnswer, $correctAnswers, true);
        }
    
        return in_array(
            strtolower($userAnswer),
            array_map('strtolower', $correctAnswers),
            true
        );
    }

    private function gradeOrdering($userAnswer, array $correctAnswers): bool
    {
        // 1. Validate inputs
        if (!is_array($userAnswer) || !is_array($correctAnswers)) {
            return false;
        }
        
        // 2. Handle empty cases
        if (empty($userAnswer) || empty($correctAnswers)) {
            return false;
        }
        
        // 3. Normalize array structures (handle both indexed and associative arrays)
        $userValues = array_values($userAnswer);
        $correctValues = array_values($correctAnswers);
        
        // 4. Check length match first for quick failure
        if (count($userValues) !== count($correctValues)) {
            return false;
        }
        
        // 5. Strict comparison of each element
        foreach ($userValues as $index => $value) {
            if (!array_key_exists($index, $correctValues)) {
                return false;
            }
            
            // Use strict comparison (type and value)
            if ($userValues[$index] !== $correctValues[$index]) {
                return false;
            }
        }
        
        return true;
    }

    private function isJsonString($string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    
    
    protected function getQuestionFeedback($question, bool $isCorrect): string
    {
        $settings = is_array($question) 
            ? ($question['settings'] ?? []) 
            : ($question->settings ?? []);
    
        if ((is_array($question) ? $question['type'] : $question->type) === 'true_false') {
            return $isCorrect 
                ? ($settings['true_feedback'] ?? 'Correct!')
                : ($settings['false_feedback'] ?? 'Incorrect');
        }
        
        return $isCorrect 
            ? ($settings['feedback'] ?? 'Correct!')
            : ($settings['feedback'] ?? 'Incorrect');
    }

    protected function gradeMatchingQuestion($userAnswer, array $correctAnswers, $options): bool
    {
        // 1. Validate inputs
        if (!is_array($options) || empty($options)) {
            return false;
        }
    
        // 2. Convert empty user answer to empty array
        if ($userAnswer === null || $userAnswer === '') {
            return false;
        }
    
        // 3. Handle JSON string input
        if (is_string($userAnswer)) {
            $userAnswer = json_decode($userAnswer, true) ?: [];
        }
    
        // 4. Build correct pairs from options (since correct_answers is empty)
        $correctPairs = [];
        foreach ($options as $pair) {
            if (isset($pair['left'], $pair['right'])) {
                $correctPairs[$pair['left']] = $pair['right'];
            }
        }
    
        // 5. Check if user answer matches all correct pairs
        foreach ($correctPairs as $left => $right) {
            if (!isset($userAnswer[$left]) || $userAnswer[$left] != $right) {
                return false;
            }
        }
    
        // 6. Verify user didn't add any incorrect matches
        foreach ($userAnswer as $left => $right) {
            if (!isset($correctPairs[$left]) || $correctPairs[$left] != $right) {
                return false;
            }
        }
    
        return true;
    }

    

    protected function processAnswerForStorage($answer)
    {
        if (is_array($answer) || is_object($answer)) {
            return json_encode($answer);
        }

        if (is_string($answer) && json_decode($answer) !== null) {
            return $answer; 
        }

        return (string) $answer;
    }
    protected function processGradingData($gradingData)
    {
        if (is_null($gradingData)) {
            return null;
        }

        return json_encode($gradingData);
    }


    public function result(Request $request, Quiz $quiz, QuizAttempt $attemptId)
    {
        
        $attempt = $attemptId ;
        if ($attempt->user_id !== auth()->id() || 
        $attempt->quiz_id !== $quiz->id ||
        !$attempt->completed_at) {
        abort(403);
        }

            // Eager load responses with their questions
        $attempt->load(['responses.question']);
        // Load the exact questions that were in this attempt
        //$questions = collect(json_decode($attempt->questions_data, true));

        // Load all questions that were in this attempt
        $questions = collect($attempt->questions_data, true);

        // Transform responses with question data
        $responses = $attempt->responses->map(function($response) {
            return [
                'question_id' => $response->question_id,
                'answer' => $this->formatAnswerForFrontend($response->answer, $response->question->type),
                'is_correct' => (bool)$response->is_correct,
                'points_earned' => $response->points_earned,
                'grading_data' => $response->grading_data ? json_decode($response->grading_data, true) : null,
            ];
        });

    

        return Inertia::render('Examinee/Result', [
            'quiz' => $quiz->only('id', 'title', 'passing_score','survey_thank_you_message','show_correct_answers'),
            'attempt' => $attempt->only([
                'id', 'score', 'max_score', 'percentage',
                'is_passed', 'completed_at', 'time_spent'
            ]),
            'questions' => $questions,
            'responses' => $responses,
        ]);
    }

    protected function formatAnswerForFrontend($answer, $questionType)
    {
        if (is_null($answer)) {
            return null;
        }
    
        // Handle JSON encoded answers
        if (is_string($answer) && json_decode($answer) !== null) {
            $answer = json_decode($answer, true);
        }
    
        // Format based on question type
        switch ($questionType) {
            case 'fill_in_the_blank':
                return is_array($answer) ? $answer : [$answer];
            case 'short_answer':
                return is_array($answer) ? $answer[0] ?? '' : $answer;
            default:
                return $answer;
        }
    }

    public function download($quizId, $attemptId)
    {
        // Similar data loading as show method
        $attempt = QuizAttempt::with([
            'quiz',
            'responses.question',
            'user'
        ])->findOrFail($attemptId);

        // Verify quiz and ownership (same as show method)

        // Generate PDF or other downloadable format
        $pdf = \PDF::loadView('exports.quiz_result', [
            'attempt' => $attempt,
            'questions' => $attempt->quiz->questions,
            'responses' => $attempt->responses
        ]);

        return $pdf->download("quiz-result-{$attemptId}.pdf");
    }

    //added system - new
    public function submit(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'answers' => 'required|array',
            'time_spent' => 'nullable|integer',
            'proctoring_flags' => 'nullable|array',
        ]);
    
        $attempt = QuizAttempt::findOrFail($validated['attempt_id']);

        if ($attempt->user_id !== auth()->id() || 
            $attempt->quiz_id !== $quiz->id ) {
            abort(403);
        }

        $questions = $this->getAttemptQuestions($attempt);

        
        $score = 0;
        $responses = [];
        $points = 0;
    
        foreach ($questions as $question) {
            $questionId = $question['id'];
            $userAnswer = $validated['answers'][$questionId] ?? null;
            // Special handling for fill-in-the-blank to preserve array structure
            if ($question['type'] === 'fill_in_the_blank' && is_string($userAnswer)) {
                $userAnswer = explode(' ', $userAnswer); // Only if frontend sends concatenated string
            }
            $result = $this->evaluateAnswer($question, $userAnswer);
    
            // Properly serialize array answers
            $answerValue = is_array($userAnswer) ? json_encode($userAnswer) : $userAnswer;
            
            $responses[] = [
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
                'answer' => $answerValue, // Now properly handles arrays
                'is_correct' => $result['is_correct'],
                'points_earned' => $result['points'],
                'time_spent' => $validated['time_spent'] ?? null,
                'grading_data' => json_encode($result['feedback']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
    
            $score += $result['points'];
            $points += $question['points'];
        }

        
        $percentageScore = round(($score/$points) * 100, 2);
        $is_passed = ($percentageScore >= $quiz->is_passed ? true : false);
    
        // Insert responses one by one to ensure proper data handling
        foreach ($responses as $response) {
            QuestionResponse::create($response);
        }
    
        $attempt->update([
            'completed_at' => now(),
            'score' => $score,
            'status' => 'completed',
            'time_spent' => $validated['time_spent'],
            'proctoring_data' => $validated['proctoring_flags'],
            'percentage' => $percentageScore,
            'max_score' => $points,
            'is_passed' => $is_passed,
        ]);
        
        return redirect()->route('examinee.quiz.results', [
            'quiz' => $quiz->id,
            'attemptId' => $attempt->id,
            'attempt' => $attempt,
        ]);
    }

    protected function evaluateAnswer(array $question, $userAnswer): array
    {
        $defaultResult = [
            'is_correct' => false,
            'points' => 0,
            'feedback' => [
                'correct_answer' => $question['correct_answers'] ?? null,
                'explanation' => null,
            ]
        ];

        // Handle null answers first
        if ($userAnswer === null) {
            return [
                'is_correct' => false,
                'points' => 0,
                'feedback' => ['correct_answer' => null, 'explanation' => 'No answer provided']
            ];
        }

        switch ($question['type']) {
            case 'multiple_choice':
                return $this->evaluateMultipleChoice($question, $userAnswer);
                
            case 'true_false':
                return $this->evaluateTrueFalse($question, $userAnswer);
                
            case 'ordering':
                return $this->evaluateOrdering($question, $userAnswer);
                
            case 'fill_in_the_blank':
                $userAnswer = is_array($userAnswer) ? implode(' ', $userAnswer) : $userAnswer;
                return $this->evaluateFillInTheBlank($question, $userAnswer);
            case 'short_answer':
                return $this->evaluateShortAnswer($question, $userAnswer);
                
            case 'matching':
                return $this->evaluateMatching($question, $userAnswer);
                
            default:
                return $defaultResult;
        }
    }

    protected function evaluateMultipleChoice(array $question, $userAnswer): array
    {
        $correctAnswers = $question['correct_answers'] ?? [];
        $isCorrect = in_array($userAnswer, $correctAnswers);
        
        return [
            'is_correct' => $isCorrect,
            'points' => $isCorrect ? ($question['points'] ?? 0) : 0,
            'feedback' => [
                'correct_answer' => $correctAnswers,
                'explanation' => $isCorrect ? 'Correct!' : 'Incorrect. The right answer was: ' . implode(', ', $correctAnswers)
            ]
        ];
    }

    protected function evaluateTrueFalse(array $question, $userAnswer): array
    {
        $correctAnswer = $question['correct_answers'][0] ?? null;
        $isCorrect = strtolower($userAnswer) === strtolower($correctAnswer);
        
        return [
            'is_correct' => $isCorrect,
            'points' => $isCorrect ? ($question['points'] ?? 0) : 0,
            'feedback' => [
                'correct_answer' => $correctAnswer,
                'explanation' => $isCorrect ? 'Correct!' : 'Incorrect. The correct answer was: ' . $correctAnswer
            ]
        ];
    }

    protected function evaluateOrdering(array $question, $userAnswer): array
    {
        $correctOrder = $question['correct_answers'] ?? [];
        $userOrder = is_array($userAnswer) ? $userAnswer : json_decode($userAnswer, true);
        
        if (!is_array($userOrder)) {
            return [
                'is_correct' => false,
                'points' => 0,
                'feedback' => [
                    'correct_answer' => $correctOrder,
                    'explanation' => 'Invalid answer format'
                ]
            ];
        }

        $isCorrect = $userOrder === $correctOrder;
        
        return [
            'is_correct' => $isCorrect,
            'points' => $isCorrect ? ($question['points'] ?? 0) : 0,
            'feedback' => [
                'correct_answer' => $correctOrder,
                'explanation' => $isCorrect ? 'Correct order!' : 'Incorrect order'
            ]
        ];
    }

    protected function evaluateFillInTheBlank(array $question, $userAnswer): array
    {
        $correctAnswers = $question['correct_answers'] ?? [];
        $settings = $question['settings'] ?? [];
        $caseSensitive = $settings['case_sensitive'] ?? false;
    
        // Ensure userAnswer is an array (frontend sends array for fill-in-the-blank)
        $userAnswers = is_array($userAnswer) ? $userAnswer : 
                      (is_string($userAnswer) ? [$userAnswer] : []);
    
        // Handle empty answers
        if (empty($userAnswers)) {
            return [
                'is_correct' => false,
                'points' => 0,
                'feedback' => [
                    'correct_answer' => $correctAnswers,
                    'explanation' => 'No answers provided'
                ]
            ];
        }
    
        // Check each blank answer
        $correctCount = 0;
        $results = [];
        
        foreach ($correctAnswers as $index => $correctAnswer) {
            $userAnswer = $userAnswers[$index] ?? '';
            $userAnswer = trim((string)$userAnswer);
            $correctAnswer = trim((string)$correctAnswer);
    
            if ($caseSensitive) {
                $isCorrect = $userAnswer === $correctAnswer;
            } else {
                $isCorrect = strtolower($userAnswer) === strtolower($correctAnswer);
            }
    
            $results[] = [
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect,
                'correct_answer' => $correctAnswer
            ];
    
            if ($isCorrect) {
                $correctCount++;
            }
        }
    
        // Calculate points (all or nothing, or partial credit)
        $allCorrect = $correctCount === count($correctAnswers);
        $points = $allCorrect 
            ? ($question['points'] ?? 0)
            : 0; // Or implement partial credit if desired
    
        return [
            'is_correct' => $allCorrect,
            'points' => $points,
            'feedback' => [
                'correct_answer' => $correctAnswers,
                'user_answers' => array_column($results, 'user_answer'),
                'explanation' => $allCorrect
                    ? 'All blanks correct!'
                    : ($correctCount > 0
                        ? "Partially correct ($correctCount/".count($correctAnswers).")"
                        : 'Incorrect answers for all blanks')
            ]
        ];
    }

    protected function evaluateShortAnswer(array $question, $userAnswer): array
    {
        $correctAnswers = $question['correct_answers'] ?? [];
        $settings = $question['settings'] ?? [];
        
        // Handle case where user didn't answer
        if (empty($userAnswer)) {
            return [
                'is_correct' => false,
                'points' => 0,
                'feedback' => [
                    'correct_answer' => $correctAnswers,
                    'explanation' => 'No answer provided'
                ]
            ];
        }

        // Normalize user answer to string
        $userAnswer = is_array($userAnswer) ? $userAnswer[0] ?? '' : (string)$userAnswer;
        $userAnswer = trim($userAnswer);

        // Check case sensitivity setting
        $caseSensitive = $settings['case_sensitive'] ?? false;
        $trimWhitespace = $settings['trim_whitespace'] ?? true;
        $ignorePunctuation = $settings['ignore_punctuation'] ?? false;

        // Normalize based on settings
        if ($trimWhitespace) {
            $userAnswer = preg_replace('/\s+/', ' ', $userAnswer);
        }

        if ($ignorePunctuation) {
            $userAnswer = preg_replace('/[^\w\s]/u', '', $userAnswer);
        }

        // Check against each correct answer
        foreach ($correctAnswers as $correct) {
            $correctAnswer = $trimWhitespace ? trim($correct) : $correct;
            
            if ($ignorePunctuation) {
                $correctAnswer = preg_replace('/[^\w\s]/u', '', $correctAnswer);
            }

            if ($caseSensitive) {
                if ($userAnswer === $correctAnswer) {
                    return [
                        'is_correct' => true,
                        'points' => $question['points'] ?? 0,
                        'feedback' => [
                            'correct_answer' => $correctAnswers,
                            'explanation' => 'Correct!'
                        ]
                    ];
                }
            } else {
                if (strtolower($userAnswer) === strtolower($correctAnswer)) {
                    return [
                        'is_correct' => true,
                        'points' => $question['points'] ?? 0,
                        'feedback' => [
                            'correct_answer' => $correctAnswers,
                            'explanation' => 'Correct!'
                        ]
                    ];
                }
            }
        }

        return [
            'is_correct' => false,
            'points' => 0,
            'feedback' => [
                'correct_answer' => $correctAnswers,
                'explanation' => 'Incorrect. Accepted answers: ' . implode(', ', $correctAnswers)
            ]
        ];
    }

    protected function evaluateMatching(array $question, $userAnswer): array
    {
        $options = $question['options'] ?? [];
        $correctPairs = [];

        // Build correct pairs from options (since correct_answers is empty for matching)
        foreach ($options as $pair) {
            if (isset($pair['left'], $pair['right'])) {
                $correctPairs[$pair['left']] = $pair['right'];
            }
        }

        // Handle case where user didn't answer
        if (empty($userAnswer)) {
            return [
                'is_correct' => false,
                'points' => 0,
                'feedback' => [
                    'correct_answer' => $correctPairs,
                    'explanation' => 'No matches provided'
                ]
            ];
        }

        // Normalize user answer (could be JSON string or array)
        if (is_string($userAnswer)) {
            $userAnswer = json_decode($userAnswer, true) ?: [];
        }

        if (!is_array($userAnswer)) {
            return [
                'is_correct' => false,
                'points' => 0,
                'feedback' => [
                    'correct_answer' => $correctPairs,
                    'explanation' => 'Invalid answer format'
                ]
            ];
        }

        // Check all pairs match
        $allCorrect = true;
        $correctCount = 0;
        $totalPairs = count($correctPairs);

        foreach ($correctPairs as $left => $right) {
            if (!isset($userAnswer[$left]) || $userAnswer[$left] != $right) {
                $allCorrect = false;
            } else {
                $correctCount++;
            }
        }

        // Check for extra incorrect matches
        foreach ($userAnswer as $left => $right) {
            if (!isset($correctPairs[$left]) || $correctPairs[$left] != $right) {
                $allCorrect = false;
            }
        }

        // Calculate partial credit if not all correct
        $points = 0;
        if ($allCorrect) {
            $points = $question['points'] ?? 0;
        } elseif ($correctCount > 0) {
            // Award partial points based on percentage correct
            $percentageCorrect = $correctCount / $totalPairs;
            $points = floor(($question['points'] ?? 0) * $percentageCorrect);
        }

        return [
            'is_correct' => $allCorrect,
            'points' => $points,
            'feedback' => [
                'correct_answer' => $correctPairs,
                'explanation' => $allCorrect 
                    ? 'All matches correct!' 
                    : ($correctCount > 0 
                        ? "Partially correct ($correctCount/$totalPairs)"
                        : 'No correct matches')
            ]
        ];
    }

}