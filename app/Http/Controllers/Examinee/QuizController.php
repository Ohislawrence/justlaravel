<?php

namespace App\Http\Controllers\Examinee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Inertia\Inertia;

class QuizController extends Controller
{
    

    // Student view
    public function show($quizId, $token = null)
    {
        $quiz = Quiz::with([
            'questions' => function($query) {
                $query->select('id', 'quiz_id', 'question', 'type', 'points', 'time_limit');
            },
            'questionPools' => function($query) {
                $query->select('question_pools.id', 'question_pools.name')
                    ->withCount('questions')
                    ->withPivot('questions_to_show');
            }
        ])->findOrFail($quizId);

        // Verify token and quiz status
        if ($quiz->share_token !== $token || !$quiz->is_published) {
            abort(404);
        }

        $isAuthenticated = auth()->check();
        $isAssigned = $isAuthenticated 
            ? $this->isUserAssigned($quiz, auth()->id())
            : false;

        return Inertia::render('Examinee/QuizLanding', [
            'quiz' => $quiz->makeVisible(['instructions', 'time_limit', 'passing_score']),
            'attemptsRemaining' => $this->getAttemptsRemaining($quiz),
            'isAuthenticated' => $isAuthenticated,
            'isAssigned' => $isAssigned,
            'organisation' => $quiz->organization->name,
        ]);
    }

    protected function checkAccessRules(Quiz $quiz)
    {
        // Check time window
        if ($quiz->starts_at && now() < $quiz->starts_at) {
            abort(403, 'This quiz is not available yet');
        }
        if ($quiz->ends_at && now() > $quiz->ends_at) {
            abort(403, 'This quiz has expired');
        }

        // Check other access rules from QuizAccessRule model
        $rules = $quiz->accessRules;
        foreach ($rules as $rule) {
            // Implement rule checking logic
        }
    }

    protected function getAttemptsRemaining(Quiz $quiz)
    {
        if (!$quiz->max_attempts) return null;
        
        $attempts = $quiz->attempts()
            ->where('user_id', auth()->id())
            ->count();
            
        return max(0, $quiz->max_attempts - $attempts);
    }

    protected function isUserAssigned(Quiz $quiz, $userId)
    {
            // Check direct user assignment
        if ($quiz->assignedUsers()->where('user_id', $userId)->exists()) {
            return true;
        }

        // Check group assignment
        if ($quiz->groups()->whereHas('members', function($query) use ($userId) {
            $query->where('users.id', $userId);
        })->exists()) {
            return true;
        }

        // Check if quiz is public
        if ($quiz->is_public) {
            return true;
        }

        return false;
    }

    
}
