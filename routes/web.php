<?php

use App\Http\Controllers\Examinee\ExamineeAuthController;
use App\Http\Controllers\Examinee\QuizAttemptController;
use App\Http\Controllers\Examinee\QuizController as ExamineeQuizController;
use App\Http\Controllers\Examiner\DashboardController;
use App\Http\Controllers\Examiner\GroupController;
use App\Http\Controllers\Examiner\PoolQuestionController;
use App\Http\Controllers\Examiner\QuestionController;
use App\Http\Controllers\Examiner\QuestionPoolAIController;
use App\Http\Controllers\Examiner\QuestionPoolController;
use App\Http\Controllers\Examiner\QuizAnalysisController;
use App\Http\Controllers\Examiner\QuizController;
use App\Http\Controllers\Examiner\ReportController;
use App\Http\Controllers\Examiner\SettingController;
use App\Http\Controllers\Examiner\UserController;
use App\Http\Controllers\FrontPageController;
use App\Http\Controllers\Landlord\BlogController;
use App\Http\Controllers\Landlord\CategoryController;
use App\Http\Controllers\Landlord\OrganizationController;
use App\Http\Controllers\Landlord\OrganizationUserController;
use App\Http\Controllers\Landlord\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaystackWebhookController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [FrontPageController::class, 'index'])->name('home');
Route::get('about-us', [FrontPageController::class, 'aboutUs'])->name('aboutus');
Route::get('contact', [FrontPageController::class, 'contact'])->name('contact');
Route::get('privacy-policy', [FrontPageController::class, 'policy'])->name('policy');
Route::get('terms-of-service', [FrontPageController::class, 'tos'])->name('tos');
Route::get('cookie-policy', [FrontPageController::class, 'cookie'])->name('cookie');
Route::get('/blogs', [FrontPageController::class, 'allBlogs'])->name('blogs.index');
Route::get('/blogs/{blog:slug}', [FrontPageController::class, 'show'])->name('blogs.show');

//sample quiz link
//Route::get('exam/{organization}/{quiz}/{uniquecode}');
Route::get('/quizzes/{quiz}/{token?}', [ExamineeQuizController::class, 'show'])
    ->name('quiz.show');

Route::post('/paystack/webhook', [PaystackWebhookController::class, 'handleWebhook']);

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
        Route::resource('users', OrganizationUserController::class);
        Route::resource('plans', OrganizationUserController::class);
        Route::resource('settings', OrganizationUserController::class);
        //Create Organization/user
        Route::resource('organizations', OrganizationController::class);
        Route::resource('organizations.users', OrganizationUserController::class);

        //blogs
        Route::resource('blogs', BlogController::class);
        Route::resource('categories', CategoryController::class);
        //Subscriptions
        Route::resource('subscriptions', SubscriptionController::class);
        // Subscription assignment routes
        Route::get('/organizations/{organization}/assign', [SubscriptionController::class, 'showAssignForm'])
            ->name('subscriptions.assign.show');
        Route::put('/organizations/{organization}/assign', [SubscriptionController::class, 'assign'])
            ->name('subscriptions.assign');

        Route::get('/organizations/select', [OrganizationController::class, 'select'])
        ->name('organizations.select');
        Route::post('/organizations/switch', [OrganizationController::class, 'switch'])
            ->name('organizations.switch');

    });

    //admin
    //admin will just have more actions than been only examiner

    //examiner
    Route::group([
        'prefix' => 'examiner',
        'middleware' => ['role:examiner','subscription'],
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

        //designation
        Route::get('users/designations', [UserController::class, 'designations'])->name('users.designations');
        Route::post('users/designations', [UserController::class, 'storeDesignation'])->name('users.designations.store');

        //Add new question pool
        Route::get('pools/{pool}/questions', [PoolQuestionController::class, 'create'])->name('pools.questions.create');
        Route::get('pools/{pool}/questions/{question}/edit', [PoolQuestionController::class, 'edit'])->name('pools.questions.edit');
        Route::put('pools/{pool}/questions/{question}/update', [PoolQuestionController::class, 'update'])->name('pools.questions.update');
        Route::post('pools/{pool}/post/questions', [PoolQuestionController::class, 'store'])->name('pools.questions.store');

        //addRemove pool to quiz
        Route::post('/quizzes/{quiz}/pools', [QuizController::class, 'attachPool'])
            ->name('quizzes.attach-pool');

        Route::delete('/quizzes/{quiz}/pools/{pool}', [QuizController::class, 'detachPool'])
            ->name('quizzes.detach-pool');

        Route::post('/quizzes/{quiz}/toggle-publish', [QuizController::class, 'togglePublish'])
        ->name('quizzes.toggle-publish');
        Route::get('/quizzes/{quiz}/pools/{pool}/questions', [QuizController::class, 'getPoolQuestions']);
        Route::resource('quizzes.questions', QuestionController::class);

        //Ai genarate question
        Route::get('/question-pools/{questionPool}/ai-generator',[QuestionPoolAIController::class, 'showAIGenerator'])
        ->name('question-pools.ai-generator');
        Route::post('/question-pools/generate-ai-questions', [QuestionPoolAIController::class, 'generate'])
        ->name('question-pools.generate-ai-questions');
    
        Route::post('/question-pools/store-ai-questions', [QuestionPoolAIController::class, 'store'])
            ->name('question-pools.store-ai-questions');

        //get Quiz Link
        Route::get('/quizz/{quiz}/share-link', [QuizController::class, 'shareLink'])->name('quizzes.share-link');

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
    
        //subcribe
        Route::get('/subscription', [PaymentController::class, 'index'])->name('subscription.plans');
        Route::post('/subscribe', [PaymentController::class, 'initializeSubscription'])->name('subscribe');
        Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');

        
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
        //login
        
        Route::post('/logout', [ExamineeAuthController::class, 'logout'])->name('logout');
        // Route::get('profile' , [ProfileController::class, 'index'])->name('myprofile');
        Route::post('/quiz/{quiz}/start', [QuizAttemptController::class, 'start'])->name('start');
        Route::get('/quiz/{quiz}/attempt', [QuizAttemptController::class, 'showAttempt'])->middleware('throttle:3,1')->name('attempt');
        Route::post('/quiz/{quiz}/submit', [QuizAttemptController::class, 'submit'])->name('submit');

        

        Route::get('/quiz/{quiz}/{attemptId}/result', [QuizAttemptController::class, 'result'])->name('quiz.results');
        
        Route::get('/quizzes/{quiz}/results/{attempt}/download', [QuizAttemptController::class, 'download'])
        ->name('quiz.results.download');
        
    });



});
