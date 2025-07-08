<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Group;
use App\Models\QuestionResponse;
use Inertia\Inertia;

class QuizAnalysisController extends Controller
{
    public function index(Quiz $quiz)
    {
        //$this->authorize('viewResults', $quiz);
        //$quiz = Quiz::find($quiz)->first();
        $attempts = $quiz->attempts()
            ->with(['user', 'responses.question'])
            ->orderBy('completed_at', 'desc')
            ->paginate(20);

        $groups = $quiz->groups()->withCount('members')->get();
        $questionStats = $this->getQuestionStatistics($quiz);

        return Inertia::render('Examiner/QuizAnalysis/Index', [
            'quiz' => $quiz,
            'attempts' => $attempts,
            'groups' => $groups,
            'questionStats' => $questionStats,
            'averageScore' => $quiz->attempts()->avg('percentage'),
            'passRate' => $quiz->attempts()->where('is_passed', true)->count() / max($quiz->attempts()->count(), 1) * 100,
        ]);
    }

    public function byGroup(Quiz $quiz, Group $group)
    {
        //$this->authorize('viewResults', $quiz);

        $attempts = $quiz->attempts()
            ->whereHas('user', function($q) use ($group) {
                $q->whereHas('groups', function($q) use ($group) {
                    $q->where('groups.id', $group->id);
                });
            })
            ->with(['user', 'responses.question'])
            ->orderBy('completed_at', 'desc')
            ->paginate(20);

        $questionStats = $this->getQuestionStatistics($quiz, $group);

        return Inertia::render('Examiner/QuizAnalysis/GroupResults', [
            'quiz' => $quiz,
            'group' => $group,
            'attempts' => $attempts,
            'questionStats' => $questionStats,
            'averageScore' => $attempts->avg('percentage'),
            'passRate' => $attempts->where('is_passed', true)->count() / max($attempts->count(), 1) * 100,
        ]);
    }

    public function questionDetail(Quiz $quiz, $questionId)
    {
        //$this->authorize('viewResults', $quiz);

        $question = $quiz->questions()->findOrFail($questionId);
        $responses = QuestionResponse::where('question_id', $questionId)
            ->whereHas('attempt', function($q) use ($quiz) {
                $q->where('quiz_id', $quiz->id);
            })
            ->with('attempt.user')
            ->paginate(20);

        return Inertia::render('Examiner/QuizAnalysis/QuestionDetail', [
            'quiz' => $quiz,
            'question' => $question,
            'responses' => $responses,
            'correctPercentage' => $responses->where('is_correct', true)->count() / max($responses->count(), 1) * 100,
        ]);
    }

    protected function getQuestionStatistics(Quiz $quiz, Group $group = null)
    {
        $query = $quiz->questions()->withCount(['responses as correct_responses' => function($q) {
            $q->where('is_correct', true);
        }]);

        if ($group) {
            $query->withCount(['responses as correct_responses' => function($q) use ($group) {
                $q->where('is_correct', true)
                  ->whereHas('attempt.user', function($q) use ($group) {
                      $q->whereHas('groups', function($q) use ($group) {
                          $q->where('groups.id', $group->id);
                      });
                  });
            }]);
        }

        return $query->get()->map(function($question) {
            return [
                'id' => $question->id,
                'question' => $question->question,
                'type' => $question->type,
                'correct_count' => $question->correct_responses,
                'total_responses' => $question->responses_count,
                'correct_percentage' => $question->responses_count > 0 
                    ? round(($question->correct_responses / $question->responses_count) * 100, 2)
                    : 0,
            ];
        });
    }


    public function export()
    {
        
    }
}
