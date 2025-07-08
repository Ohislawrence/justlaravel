<?php

use App\Http\Controllers\Examinee\QuizAttemptController;
use App\Http\Controllers\Examinee\QuizController as ExamineeQuizController;
use App\Http\Controllers\Examiner\DashboardController;
use App\Http\Controllers\Examiner\GroupController;
use App\Http\Controllers\Examiner\QuestionController;
use App\Http\Controllers\Examiner\QuestionPoolController;
use App\Http\Controllers\Examiner\QuizAnalysisController;
use App\Http\Controllers\Examiner\QuizController;
use App\Http\Controllers\Examiner\ReportController;
use App\Http\Controllers\Examiner\SettingController;
use App\Http\Controllers\Examiner\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

//sample quiz link
//Route::get('exam/{organization}/{quiz}/{uniquecode}');
Route::get('/quizzes/{quiz}/{token?}', [ExamineeQuizController::class, 'show'])
    ->name('quiz.show');





Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    

    Route::get('dashboard', function () {
    
        if (auth()->user()->hasRole('landlord')){
            return redirect(route('landlord.dashboard'));
        }
        elseif(auth()->user()->hasRole('admin')){
            return redirect(route('admin.dashboard'));
        }
        elseif(auth()->user()->hasRole('examiner')){
            return redirect(route('examiner.dashboard'));
        }
        elseif(auth()->user()->hasRole('examinee')){
            return redirect(route('examinee.dashboard'));
        }
        else{
            return redirect(route('dashboard'));
        }
    })->name('dashboard');

    Route::get('home', function () {
    
        if (auth()->user()->hasRole('landlord')){
            return redirect(route('landlord.dashboard'));
        }
        elseif(auth()->user()->hasRole('examiner')){
            return redirect(route('examiner.dashboard'));
        }
        elseif(auth()->user()->hasRole('examinee')){
            return redirect(route('examinee.dashboard'));
        }
        else{
            return redirect(route('dashboard'));
        }
    })->name('home');


    //landlord
    Route::group([
        'prefix' => 'landlord',
        'middleware' => 'role:landlord',
        'as' => 'landlord.',
    ], function () {
        //Route::view('dashboard', 'dashboard')->name('dashboard');
        Route::get('dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');
    });

    //admin
    //admin will just have more actions than been only examiner

    //examiner
    Route::group([
        'prefix' => 'examiner',
        'middleware' => 'role:examiner',
        'as' => 'examiner.',
    ], function () {
        //Route::view('dashboard', 'dashboard')->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index'] )->name('dashboard');
        Route::resource('user', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        //Route::resource('groups', GroupController::class)->except(['edit'])
        //->scoped(['organization' => 'slug']);
        Route::prefix('groups')->name('groups.')->group(function () {
            Route::resource('/', GroupController::class)->except(['edit'])
            ->scoped(['organization' => 'slug'])->parameters(['' => 'group']);
            Route::post('/add-members/{group}', [GroupController::class, 'addMembers'])->name('addMembers');
            Route::delete('/remove-member/{group}/{user}', [GroupController::class, 'removeMember'])->name('removeMember');
            Route::post('/assign-quizzes/{group}', [GroupController::class, 'assignQuizzes'])->name('assignQuizzes');
            Route::delete('/remove-quiz/{group}', [GroupController::class, 'removeQuiz'])->name('removeQuiz');
        });
        Route::resource('reports', ReportController::class);
        Route::resource('settings', SettingController::class);
        Route::resource('quizzes', QuizController::class);
        Route::post('/quizzes/{quiz}/toggle-publish', [QuizController::class, 'togglePublish'])
        ->name('quizzes.toggle-publish');
        Route::get('/quizzes/{quiz}/pools/{pool}/questions', [QuizController::class, 'getPoolQuestions']);
         Route::resource('quizzes.questions', QuestionController::class);
        //get Quiz Link
        Route::get('/quizz/{quiz}/share-link', [QuizController::class, 'shareLink'])->name('quizz.share-link');

        //attempt details
        Route::get('examiner/quizzes/{quiz}/attempts/{attempt}', [QuizController::class, 'showAttempts'])
            ->name('quiz.attempts.show');

         Route::resource('question-pools', QuestionPoolController::class)
            ->only(['index', 'store','edit' ,'show','create', 'delete']);
        Route::get('question-pools/{pool}/questions', [QuestionPoolController::class, 'manageQuestions'])->name('question-pools.manage-questions');

        Route::post('question-pools/{pool}/questions', [QuestionPoolController::class, 'attachQuestions'])
            ->name('pools.attach-questions');

        Route::delete('question-pools/{pool}/questions/{question}', [QuestionPoolController::class, 'detachQuestion'])
            ->name('pools.detach-question');

        //analyze
        Route::get('quizzes/{quiz}/analysis', [QuizAnalysisController::class, 'index'])
            ->name('quizzes.analysis.index');
            
        Route::get('quizzes/{quiz}/analysis/group/{group}', [QuizAnalysisController::class, 'byGroup'])
            ->name('quizzes.analysis.group');
            
        Route::get('quizzes/{quiz}/analysis/questions/{questionId}', [QuizAnalysisController::class, 'questionDetail'])
            ->name('quizzes.analysis.question');
        Route::post('/quizzes/{quiz}/analysis/export/{group}', [QuizAnalysisController::class, 'export'])
            ->name('examiner.quizzes.analysis.export');
    
        
    });

    //examinee
    Route::group([
        'prefix' => 'examinee',
        'middleware' => 'role:examinee',
        'as' => 'examinee.',
    ], function () {


        Route::get('dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');
        // Route::get('profile' , [ProfileController::class, 'index'])->name('myprofile');
        Route::post('/quiz/{quiz}/start', [QuizAttemptController::class, 'start'])->name('start');
        Route::post('/quiz/{quiz}/submit', [QuizAttemptController::class, 'submit'])->name('submit');

        Route::get('/quiz/{quiz}/attempt', [QuizAttemptController::class, 'start'])->name('attempt');

        Route::get('/quiz/{quiz}/{attemptId}/result', [QuizAttemptController::class, 'result'])->name('quiz.results');
        
        Route::get('/quizzes/{quiz}/results/{attempt}/download', [QuizAttemptController::class, 'download'])
        ->name('quiz.results.download');
        
    });



});
