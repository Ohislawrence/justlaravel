<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Group;
use App\Models\QuestionResponse;
use Inertia\Inertia;
use App\Services\QuizAnalysisService;

class QuizAnalysisController extends Controller
{
    public function index(Quiz $quiz)
    {
        $service = new QuizAnalysisService($quiz);

       // dd($service->getGeneralAnalysis()['question_stats']);
        return Inertia::render('Examiner/QuizAnalysis/Index', [
            'quiz' => $quiz->load('groups', 'questionPools'),
            'analysis' => $service->getGeneralAnalysis(),
            'groups' => $quiz->groups()->withCount('members')->latest()->get(),
            'attempts' => $quiz->attempts()->with('user')->latest()->paginate(10),
            'questionStats' => $service->getGeneralAnalysis()['question_stats'],
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
            // Add this for charts
            'allAttempts' => $analysis['attempts']->items(),
        ]);
    }

    public function questionDetail(Quiz $quiz, $questionId)
    {
        //$this->authorize('viewResults', $quiz);

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

        return Inertia::render('Examiner/QuizAttempts/Show', [
            'quiz' => $quiz,
            'question' => $service->getGeneralAnalysis()['question_stats'],
            'responses' => $service->getQuestionDetailAnalysis($attempt)['responses'],
        ]);
    }

    

    public function export()
    {
        
    }
}
