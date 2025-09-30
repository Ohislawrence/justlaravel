<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Group;
use App\Models\ProctoringViolation;
use App\Models\QuestionResponse;
use App\Models\User;
use Inertia\Inertia;
use App\Services\QuizAnalysisService;

class QuizAnalysisController extends Controller
{
    public function index(Quiz $quiz)
    {
        $service = new QuizAnalysisService($quiz);
        $analysis = $service->getGeneralAnalysis();

        //dd($analysis['question_stats']);
        return Inertia::render('Examiner/QuizAnalysis/Index', [
            'quiz' => $quiz->load('groups', 'questionPools'),
            'questionpools' => (object)$quiz->questionPools,
            'analysis' => $service->getGeneralAnalysis(),
            'groups' => $quiz->groups()->withCount('members')->latest()->get(),
            'attempts' => $quiz->attempts()
                ->with('user')
                ->whereNotNull('completed_at')
                ->latest()
                ->paginate(20),
            'attempt' => $quiz->attempts()->with(['responses.question'])
                    ->where('quiz_id', $quiz->id)
                    ->get(),
            'questionStats' => $analysis['question_stats'],
        ]);
    }

    public function byGroup(Quiz $quiz, Group $group)
    {
        $service = new QuizAnalysisService($quiz, $group);
        $analysis = $service->getGeneralAnalysis();

        //dd($analysis['attempts']);

        return Inertia::render('Examiner/QuizAnalysis/GroupResults', [
            'quiz' => $quiz->load('groups', 'questionPools'), // Match what's in index()
            'group' => $group->loadCount('members'), // Ensure members_count is available
            'attempts' => $analysis['attempts']->toArray(), // Already paginated from getGeneralAnalysis()
            'questionStats' => $analysis['question_stats'],
            'averageScore' => $analysis['average_score'],
            'passRate' => $analysis['pass_rate'],
            'groups' => $quiz->groups()->withCount('members')->get(), // For group switcher
            'totalAttempts' => $analysis['total_attempts'],
            'allAttempts' => $analysis['attempts']->items(),
        ]);
    }

    public function byUser(Quiz $quiz, User $user)
    {
        $service = new QuizAnalysisService($quiz);

        return Inertia::render('Examiner/QuizAnalysis/UserAttempts', [
            'quiz' => $quiz->load('groups', 'questionPools'),
            'user' => $user,
            'attempts' => $quiz->attempts()
                ->where('user_id', $user->id)
                ->with('user')
                ->whereNotNull('completed_at')
                ->latest()
                ->paginate(10),
            'questionStats' => $service->getGeneralAnalysis()['question_stats'],
            'averageScore' => $quiz->attempts()
                ->where('user_id', $user->id)
                ->avg('percentage') ?? 0,
            'bestScore' => $quiz->attempts()
                ->where('user_id', $user->id)
                ->max('percentage') ?? 0,
        ]);
    }

    public function questionDetail(Quiz $quiz, $questionId)
    {

        $service = new QuizAnalysisService($quiz);

        return Inertia::render('Examiner/QuizAnalysis/QuestionDetail', [
            'quiz' => $quiz,
            'question' => $service->getQuestionDetailAnalysis($questionId)['question'],
            'responses' => $service->getQuestionDetailAnalysis($questionId)['responses'],
            'stats' => $service->getQuestionDetailAnalysis($questionId)['stats']
        ]);
    }

    public function quizAttempt(Quiz $quiz, QuizAttempt $attempt)
    {
        $service = new QuizAnalysisService($quiz);
        //dd($quiz->questionPools);
        return Inertia::render('Examiner/QuizAttempts/Show', [
            'quiz' => $quiz,
            'questionpools' => (object)$quiz->questionPools,
            'question' => $service->getGeneralAnalysis()['question_stats'],
            'responses' => $service->getQuestionDetailAnalysis($attempt)['responses'],
        ]);
    }

    public function getViolations($attemptId)
    {
        $violations = ProctoringViolation::where('quiz_attempt_id', $attemptId)
        ->orderBy('violation_time', 'desc')
        ->get();

        return inertia('Proctoring/Violations', [
            'violations' => $violations,
            'attemptId' => $attemptId
        ]);
    }

    

    public function export()
    {
        
    }
}
