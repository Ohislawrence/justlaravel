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

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $request->user()->id,
            'attempt_number' => $this->getNextAttemptNumber($quiz, $request->user()->id),
            'started_at' => now(),
            'status' => 'in_progress'
        ]);

        // Prepare questions
        $questions = $this->prepareQuestions($quiz);
        
        // Return Inertia response
        return Inertia::render('Examinee/QuizPlayer', [
            'quiz' => $quiz->only('id', 'title', 'time_limit'),
            'attempt' => $attempt,
            'questions' => $questions,
            'time_limit' => $quiz->time_limit
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
        
        // Process question pools
        foreach ($quiz->questionPools as $pool) {
            $poolQuestions = $pool->questions()
                ->inRandomOrder()
                ->take($pool->questions_to_show)
                ->get()
                ->map(function ($question) {
                    return $this->formatQuestion($question);
                });
                
            $questions = $questions->merge($poolQuestions);
        }
        
        // Add regular questions
        $questions = $questions->merge(
            $quiz->questions->map(function ($question) {
                return $this->formatQuestion($question);
            })
        );
        
        // Randomize if needed
        if ($quiz->randomize_questions) {
            $questions = $questions->shuffle();
        }
        
        return $questions->values();
    }

    protected function formatQuestion($question)
    {
        return [
            'id' => $question->id,
            'question' => $question->question,
            'type' => $question->type,
            'points' => $question->points,
            'time_limit' => $question->time_limit,
            'options' => $question->options,
            'correct_answers' => $question->correct_answers,
            'image'  => $question->image,
            'audio'  => $question->audio,
            'video'  => $question->video,
            'order'  => $question->order,
            'is_required'  => $question->is_required,
            'settings'  => $question->settings,
        ];
    }

    public function submit(Request $request, Quiz $quiz)
    {
        //dd($request->all());
        $validated = $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'answers' => 'required|array',
            'time_spent' => 'nullable|integer|min:0',
            'proctoring_flags' => 'nullable|array',
            'answers.*' => 'nullable', // Allow null answers
            'proctoring_flags.tab_switches' => 'nullable|integer|min:0',
            'proctoring_flags.fullscreen_exits' => 'nullable|integer|min:0',
        ]);

        // Normalize answers - ensure question IDs are integers
        $answers = [];
        foreach ($validated['answers'] ?? [] as $questionId => $answer) {
            $answers[(int)$questionId] = $answer;
        }

        $attemptId = DB::transaction(function () use ($quiz, $validated, $answers) {
            $attempt = QuizAttempt::where('id', $validated['attempt_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($attempt->completed_at) {
                abort(403, 'This quiz attempt has already been submitted.');
            }

            $results = $this->processQuizSubmission($quiz, $answers);

            // Ensure all required fields have values
            $updateData = [
                'completed_at' => now(),
                'time_spent' => $validated['time_spent'] ?? $attempt->time_spent ?? 0,
                'score' => $results['score'] ?? 0,
                'max_score' => $results['max_score'] ?? 0,
                'percentage' => $results['percentage'] ?? 0,
                'is_passed' => ($results['percentage'] ?? 0) >= $quiz->passing_score,
                'answers' => json_encode($answers),
                'grading_data' => json_encode($results['grading_data'] ?? []),
            ];

            $attempt->update($updateData);

            $this->storeQuestionResponses($attempt, $answers, $results['grading_data'] ?? []);

            ActivityService::logQuizCompleted($attempt);
            return $attempt->id;
           
        });
        
        return redirect()->route('examinee.quiz.results', [
            'quiz' => $quiz->id,
            'attemptId' => $attemptId
        ]);
    }

    protected function processQuizSubmission(Quiz $quiz, array $answers): array
    {
        $maxScore = 0;
        $earnedScore = 0;
        $gradingData = [];
    
        foreach ($quiz->questions as $question) {
            $maxScore += $question->points;
    
            // Check if answer exists for this question
            if (!array_key_exists($question->id, $answers)) {
                $gradingData[$question->id] = [
                    'is_correct' => false,
                    'points_earned' => 0,
                    'feedback' => 'No answer provided'
                ];
                continue;
            }
    
            $userAnswer = $answers[$question->id];
            
            // Handle correct_answers - ensure it's properly formatted
            $correctAnswers = $question->correct_answers;
            if (is_string($correctAnswers)) {
                $decoded = json_decode($correctAnswers, true);
                $correctAnswers = $decoded !== null ? $decoded : [$correctAnswers];
            }
            
            if (!is_array($correctAnswers)) {
                $correctAnswers = [$correctAnswers];
            }
    
            $isCorrect = $this->gradeQuestion($question, $userAnswer, $correctAnswers);
    
            if ($isCorrect) {
                $earnedScore += $question->points;
            }
    
            $gradingData[$question->id] = [
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question->points : 0,
                'feedback' => $this->getQuestionFeedback($question, $isCorrect)
            ];
        }
    
        return [
            'score' => $earnedScore,
            'max_score' => $maxScore,
            'percentage' => $maxScore > 0 ? round(($earnedScore / $maxScore) * 100, 2) : 0,
            'grading_data' => $gradingData,
        ];
    }

    protected function gradeQuestion(Question $question, $userAnswer, array $correctAnswers): bool
    {
        // Skip null or empty answers for non-required questions
        if ($userAnswer === null || $userAnswer === '') {
            return false;
        }

        // Handle JSON strings in user answers
        if (is_string($userAnswer) && $this->isJsonString($userAnswer)) {
            $userAnswer = json_decode($userAnswer, true);
        }

        switch ($question->type) {
            case 'multiple_choice':
                return $this->gradeMultipleChoiceQuestion($userAnswer, $correctAnswers);
            case 'true_false':
                return $this->gradeTrueFalseQuestion($userAnswer, $correctAnswers);

            case 'fill_in_the_blank':
                return $this->gradeFillInTheBlank($userAnswer, $correctAnswers, $question->settings ?? []);

            case 'short_answer':
                return $this->gradeShortAnswer($userAnswer, $correctAnswers, $question->settings ?? []);

            case 'ordering':
                return $this->gradeOrdering($userAnswer, $correctAnswers);

            case 'matching':
                return $this->gradeMatchingQuestion($userAnswer, $correctAnswers, $question);

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
        return $correctAnswer;
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
        // Handle array input - take first element
        $userAnswer = is_array($userAnswer) ? ($userAnswer[0] ?? '') : $userAnswer;
        $userAnswer = trim($userAnswer ?? '');
        
        // Clean correct answers
        $correctAnswers = array_map('trim', $correctAnswers);
        
        // Check case sensitivity
        $caseSensitive = $settings['case_sensitive'] ?? false;
        
        if ($caseSensitive) {
            return in_array($userAnswer, $correctAnswers);
        }
        
        return in_array(strtolower($userAnswer), array_map('strtolower', $correctAnswers));
    }

    private function gradeOrdering($userAnswer, array $correctAnswers): bool
    {
        // Ensure both are arrays with same structure
        if (!is_array($userAnswer)) {
            return false;
        }
        
        // Compare ordered arrays
        return array_values($userAnswer) === array_values($correctAnswers);
    }

    private function isJsonString($string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    
    
    protected function getQuestionFeedback(Question $question, bool $isCorrect): string
    {
        // Special handling for true/false questions
        if ($question->type === 'true_false') {
            return $isCorrect 
                ? ($question->settings['true_feedback'] ?? 'Correct!')
                : ($question->settings['false_feedback'] ?? 'Incorrect');
        }
        
        // Default feedback for other question types
        return $isCorrect 
            ? ($question->settings['feedback'] ?? 'Correct!')
            : ($question->settings['feedback'] ?? 'Incorrect');
    }

    protected function gradeMatchingQuestion($userAnswer, array $correctAnswers, Question $question): bool
    {
        // Get the correct pairs from options
        $correctPairs = $question->options ?? [];
        
        // If user answer is not an array or empty, return false
        if (!is_array($userAnswer) || empty($userAnswer)) {
            return false;
        }
        
        // If no correct pairs exist, return false
        if (empty($correctPairs)) {
            return false;
        }
        
        // Create a map of correct answers from the options
        $correctMap = [];
        foreach ($correctPairs as $pair) {
            if (isset($pair['left'], $pair['right'])) {
                $correctMap[$pair['left']] = $pair['right'];
            }
        }
        
        // Check each user answer against the correct map
        foreach ($userAnswer as $left => $right) {
            if (!isset($correctMap[$left]) || $correctMap[$left] != $right) {
                return false;
            }
        }
        
        // Verify all correct pairs were matched
        foreach ($correctMap as $left => $right) {
            if (!isset($userAnswer[$left]) || $userAnswer[$left] != $right) {
                return false;
            }
        }
        
        return true;
    }

    protected function storeQuestionResponses(QuizAttempt $attempt, array $answers, array $gradingData): void
    {
        foreach ($answers as $questionId => $answer) {
            QuestionResponse::create([
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
                'answer' => $this->processAnswerForStorage($answer),
                'points_earned' => $gradingData[$questionId]['points_earned'] ?? 0,
                'is_correct' => $gradingData[$questionId]['is_correct'] ?? false,
                'grading_data' => $this->processGradingData($gradingData[$questionId] ?? null),
            ]);
        }
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
        // Verify the attempt belongs to the authenticated user
        if ($attempt->user_id !== auth()->user()->id) {
            abort(403);
        }

        // Verify the attempt belongs to the quiz
        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }

        // Verify the attempt is completed
        if (!$attempt->completed_at) {
            return redirect()->route('examinee.quiz.show', $quiz);
        }

        // Load all related data
        $attempt->load([
            'quiz',
            'responses.question'
        ]);

        // Prepare questions with their responses
        $questions = $quiz->questions()->with(['responses' => function($query) use ($attempt) {
            $query->where('attempt_id', $attempt->id);
        }])->get();

        // Transform data for the frontend
        $transformedQuestions = $questions->map(function($question) use ($attempt) {
            $response = $question->responses->first();
            
            return [
                'id' => $question->id,
                'type' => $question->type,
                'question' => $question->question,
                'points' => $question->points,
                'explanation' => $question->explanation,
                'options' => $question->options, 
                'correct_answers' => $question->correct_answers, 
                // Add any other question fields you need
            ];
        });

        $transformedResponses = $attempt->responses->map(function($response) {
            return [
                'question_id' => $response->question_id,
                'answer' => $this->formatAnswerForFrontend($response->answer, $response->question->type),
                'is_correct' => (bool)$response->is_correct,
                'points_earned' => $response->points_earned,
                'grading_data' => $response->grading_data ? json_decode($response->grading_data, true) : null,
            ];
        });

        return Inertia::render('Examinee/Result', [
            'quiz' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'passing_score' => $quiz->passing_score,
            ],
            'attempt' => [
                'id' => $attempt->id,
                'score' => $attempt->score,
                'max_score' => $attempt->max_score,
                'percentage' => $attempt->percentage,
                'is_passed' => $attempt->is_passed,
                'completed_at' => $attempt->completed_at,
                'time_spent' => $attempt->time_spent,
            ],
            'questions' => $transformedQuestions,
            'responses' => $transformedResponses,
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

}