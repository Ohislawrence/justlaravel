<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\Group;
use App\Models\Question;
use Illuminate\Support\Collection;

class QuizAnalysisService
{
    protected $quiz;
    protected $groupFilter;

    public function __construct(Quiz $quiz, ?Group $group = null)
    {
        $this->quiz = $quiz;
        $this->groupFilter = $group;
    }

    public function getGeneralAnalysis()
    {
        $attemptsQuery = $this->getFilteredAttempts();
        $attempts = $attemptsQuery->get();
        $paginatedAttempts = $attemptsQuery->paginate(10);

        $questions = $this->getAllQuizQuestions();
        
        return [
            'total_attempts' => $attempts->count(),
            'average_score' => $attempts->avg('percentage') ?? 0,
            'pass_rate' => $this->calculatePassRate($attempts),
            'question_stats' => $this->getQuestionStatistics($questions, $attempts),
            'time_stats' => $this->getTimeStatistics($attempts),
            'attempts' => $paginatedAttempts,
            'question_sources' => $this->getQuestionSources()
        ];
    }


    protected function getAllQuizQuestions()
    {
        // Get direct questions
        $directQuestions = $this->quiz->questions;
        
        // Get questions from pools
        $poolQuestions = collect();
        $this->quiz->questionPools->each(function ($pool) use (&$poolQuestions) {
            $questions = $pool->questions()
                ->inRandomOrder()
                ->take($pool->pivot->questions_to_show ?? $pool->questions->count())
                ->get();
            $poolQuestions = $poolQuestions->merge($questions);
        });
        
        return $directQuestions->merge($poolQuestions);
    }

    protected function getQuestionSources()
    {
        // Ensure questions relationship is loaded
        if (!$this->quiz->relationLoaded('questions')) {
            $this->quiz->load('questions');
        }
        
        // Ensure questionPools relationship is loaded with necessary data
        if (!$this->quiz->relationLoaded('questionPools')) {
            $this->quiz->load(['questionPools' => function($query) {
                $query->withCount('questions')
                      ->withPivot('questions_to_show');
            }]);
        }
    
        return [
            'direct' => $this->quiz->questions->count(),
            'pools' => $this->quiz->questionPools->map(function ($pool) {
                // Safely get questions_to_show from pivot
                $questionsToShow = $pool->pivot->questions_to_show ?? null;
                
                // Calculate how many questions are actually used
                $questionsUsed = min(
                    $questionsToShow !== null ? $questionsToShow : $pool->questions_count,
                    $pool->questions_count
                );
    
                return [
                    'pool_id' => $pool->id,
                    'pool_name' => $pool->name,
                    'questions_count' => $pool->questions_count,
                    'questions_to_show' => $questionsToShow,
                    'questions_used' => $questionsUsed,
                    'is_global' => $pool->is_global ?? false
                ];
            })->filter() // Remove any null entries
        ];
    }
    
    protected function getFilteredAttempts()
    {
        $query = $this->quiz->attempts()
            ->with('user')
            ->whereNotNull('completed_at');
            
        if ($this->groupFilter) {
            $query->whereHas('user.groups', function ($q) {
                $q->where('groups.id', $this->groupFilter->id);
            });
        }
        
        return $query;
    }

    public function getGroupAnalysis()
    {
        $groups = $this->quiz->groups()->withCount('members')->get();
        
        return $groups->map(function ($group) {
            $service = new self($this->quiz, $group);
            $analysis = $service->getGeneralAnalysis();
            
            return [
                'id' => $group->id,
                'name' => $group->name,
                'members_count' => $group->members_count,
                'total_attempts' => $analysis['total_attempts'],
                'average_score' => $analysis['average_score'],
                'pass_rate' => $analysis['pass_rate']
            ];
        });
    }

    public function getQuestionDetailAnalysis($questionId)
    {
        $attempts = $this->getFilteredAttempts();
        $responses = collect();
        
        $attempts->each(function ($attempt) use (&$responses, $questionId) {
            $gradingData = collect($attempt->grading_data['questions'] ?? [])
                ->firstWhere('id', $questionId);
                
            if ($gradingData) {
                $responses->push([
                    'attempt_id' => $attempt->id,
                    'user' => $attempt->user,
                    'answer' => $gradingData['user_answer'] ?? null,
                    'is_correct' => $gradingData['is_correct'] ?? false,
                    'points_earned' => $gradingData['points_earned'] ?? 0,
                    'time_spent' => $gradingData['time_spent'] ?? 0,
                    'feedback' => $gradingData['feedback'] ?? null
                ]);
            }
        });
        
        $question = collect($attempts->first()->grading_data['questions'] ?? [])
            ->firstWhere('id', $questionId);
            
        return [
            'question' => $question,
            'responses' => $responses->paginate(10),
            'stats' => $this->getQuestionStats($responses, $question)
        ];
    }

    

    protected function calculatePassRate($attempts)
    {
        // Ensure $attempts is a collection
        if ($attempts instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
            $attempts = $attempts->get();
        }
        
        if ($attempts->isEmpty()) return 0;
        
        $passingScore = $this->quiz->passing_score ?? 70;
        $passed = $attempts->filter(fn($a) => $a->percentage >= $passingScore)->count();
        
        return ($passed / $attempts->count()) * 100;
    }

    protected function getQuestionStatistics(Collection $questions, Collection $attempts)
    {
        return $questions->map(function ($question) use ($attempts) {
            $responses = $this->getQuestionResponses($question->id, $attempts);
            
            return [
                'id' => $question->id,
                'question' => $question->question,
                'type' => $question->type,
                'points' => $question->points,
                'source' => $this->getQuestionSource($question),
                'correct_responses' => $responses->where('is_correct', true)->count(),
                'total_responses' => $responses->count(),
                'average_points' => $responses->avg('points_earned') ?? 0,
                'average_time' => $responses->avg('time_spent') ?? 0,
                'difficulty' => $this->calculateDifficulty(
                    $responses->where('is_correct', true)->count(),
                    $responses->count()
                )
            ];
        });
    }

    protected function getQuestionSource(Question $question)
    {
        // First check if question is directly attached to the quiz
        if ($this->quiz->questions->contains('id', $question->id)) {
            return [
                'type' => 'direct',
                'source' => 'Attached directly to quiz'
            ];
        }
    
        // Check if question comes from any of the quiz's pools
        foreach ($this->quiz->questionPools as $pool) {
            if ($pool->questions->contains('id', $question->id)) {
                return [
                    'type' => 'pool',
                    'pool_id' => $pool->id,
                    'pool_name' => $pool->name,
                    'questions_to_show' => $pool->pivot->questions_to_show ?? null,
                    'source' => "From pool: {$pool->name}"
                ];
            }
        }
    
        // Fallback for questions with unknown source
        return [
            'type' => 'unknown',
            'source' => 'Unknown source'
        ];
    }

    protected function getQuestionResponses(int $questionId, Collection $attempts): Collection
    {
        return $attempts->flatMap(function ($attempt) use ($questionId) {
            // Get responses from the attempt's grading data
            $gradingData = collect($attempt->grading_data['questions'] ?? []);
            
            // Find responses for this specific question
            return $gradingData->filter(function ($questionData) use ($questionId) {
                return ($questionData['id'] ?? null) == $questionId;
            })->map(function ($questionData) use ($attempt) {
                return [
                    'attempt_id' => $attempt->id,
                    'user_id' => $attempt->user_id,
                    'user' => $attempt->user,
                    'answer' => $questionData['user_answer'] ?? null,
                    'is_correct' => $questionData['is_correct'] ?? false,
                    'points_earned' => $questionData['points_earned'] ?? 0,
                    'time_spent' => $questionData['time_spent'] ?? 0,
                    'feedback' => $questionData['feedback'] ?? null,
                    'completed_at' => $attempt->completed_at
                ];
            });
        });
    }

    protected function getTimeStatistics($attempts)
    {
        return [
            'average_time' => $attempts->avg('time_spent') ?? 0,
            'fastest_completion' => $attempts->min('time_spent') ?? 0,
            'slowest_completion' => $attempts->max('time_spent') ?? 0
        ];
    }

    protected function getQuestionStats($responses, $question)
    {
        $total = $responses->count();
        $correct = $responses->where('is_correct', true)->count();
        
        return [
            'total_responses' => $total,
            'correct_responses' => $correct,
            'correct_percentage' => $total > 0 ? ($correct / $total) * 100 : 0,
            'average_points' => $responses->avg('points_earned') ?? 0,
            'average_time' => $responses->avg('time_spent') ?? 0,
            'difficulty' => $this->calculateDifficulty($correct, $total),
            'options_stats' => $this->getOptionsStats($question, $responses)
        ];
    }

    protected function getOptionsStats($question, $responses)
    {
        if (!in_array($question['type'], ['multiple_choice', 'true_false', 'matching'])) {
            return null;
        }
        
        $stats = [];
        $options = $question['options'] ?? [];
        
        foreach ($options as $index => $option) {
            $text = is_array($option) ? ($option['text'] ?? $option['value'] ?? $index) : $option;
            $isCorrect = in_array($index, (array)($question['correct_answers'] ?? []));
            
            $count = $responses->filter(function ($response) use ($index, $question) {
                if ($question['type'] === 'matching') {
                    return in_array($index, (array)$response['answer']);
                }
                return $response['answer'] == $index;
            })->count();
            
            $stats[] = [
                'option' => $text,
                'count' => $count,
                'is_correct' => $isCorrect
            ];
        }
        
        return $stats;
    }

    protected function calculateDifficulty($correct, $total)
    {
        if ($total === 0) return 'N/A';
        
        $percentage = ($correct / $total) * 100;
        
        if ($percentage >= 80) return 'Easy';
        if ($percentage >= 50) return 'Medium';
        return 'Hard';
    }
}
