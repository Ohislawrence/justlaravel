<?php

use App\Http\Controllers\API\ProctoringController;
use App\Http\Controllers\CertificateController as ControllersCertificateController;
use App\Http\Controllers\Examinee\DashboardController as ExamineeDashboardController;
use App\Http\Controllers\Examinee\ExamineeAuthController;
use App\Http\Controllers\Examinee\QuizAttemptController;
use App\Http\Controllers\Examinee\QuizController as ExamineeQuizController;
use App\Http\Controllers\Examiner\CertificateController;
use App\Http\Controllers\Examiner\CertificateTemplateController;
use App\Http\Controllers\Examiner\DashboardController;
use App\Http\Controllers\Examiner\GradingSystemController;
use App\Http\Controllers\Examiner\GroupController;
use App\Http\Controllers\Examiner\PoolQuestionController;
use App\Http\Controllers\Examiner\QuestionController;
use App\Http\Controllers\Examiner\QuestionPoolAIController;
use App\Http\Controllers\Examiner\QuestionPoolController;
use App\Http\Controllers\Examiner\QuizAnalysisController;
use App\Http\Controllers\Examiner\QuizController;
use App\Http\Controllers\Examiner\QuizGroupController;
use App\Http\Controllers\Examiner\ReportController;
use App\Http\Controllers\Examiner\SettingController;
use App\Http\Controllers\Examiner\UserController;
use App\Http\Controllers\FrontPageController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\Landlord\BlogController;
use App\Http\Controllers\Landlord\CategoryController;
use App\Http\Controllers\Landlord\HelpCenterController as LandlordHelpCenterController;
use App\Http\Controllers\Landlord\OrganizationController;
use App\Http\Controllers\Landlord\OrganizationUserController;
use App\Http\Controllers\Landlord\SubscriptionController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaystackWebhookController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [FrontPageController::class, 'index'])->name('home');
Route::get('pricing', [FrontPageController::class, 'pricing'])->name('pricing');
Route::get('features', [FrontPageController::class, 'features'])->name('features');
Route::get('about-us', [FrontPageController::class, 'aboutUs'])->name('aboutus');
Route::get('contact', [FrontPageController::class, 'contact'])->name('contact');
Route::get('privacy-policy', [FrontPageController::class, 'policy'])->name('policy');
Route::get('terms-of-service', [FrontPageController::class, 'tos'])->name('tos');
Route::get('cookie-policy', [FrontPageController::class, 'cookie'])->name('cookie');
Route::get('/blogs', [FrontPageController::class, 'allBlogs'])->name('blogs.index');
Route::get('/blogs/{blog:slug}', [FrontPageController::class, 'show'])->name('blogs.show');
Route::prefix('for')->group(function () {
    Route::get('hr', [FrontPageController::class, 'hr'])->name('hr');
    Route::get('learning-development', [FrontPageController::class, 'ld'])->name('learning-development');
    Route::get('certifications', [FrontPageController::class, 'certifications'])->name('certifications');
    Route::get('schools', [FrontPageController::class, 'schools'])->name('schools');
});
Route::prefix('help')->group(function () {
    Route::get('/', [HelpCenterController::class, 'index'])->name('help.index');
    Route::get('/search', [HelpCenterController::class, 'search'])->name('help.search');
    Route::get('/{slug}', [HelpCenterController::class, 'showCategory'])->name('help.category');
    Route::get('/{categorySlug}/{articleSlug}', [HelpCenterController::class, 'showArticle'])->name('help.article');
});

//quiz link
Route::get('/quizzes/{quiz}/{token?}', [ExamineeQuizController::class, 'show'])
    ->name('quiz.show');

//guest
Route::post('/public/quiz/{quiz}/start/guest', [QuizAttemptController::class, 'start'])->name('quiz.start.guest');
Route::get('/public/quiz/{quiz}/show/guest', [QuizAttemptController::class, 'showAttempt'])->name('quiz.show.guest');
Route::post('/public/quiz/{quiz}/start', [QuizAttemptController::class, 'submit'])->name('quiz.submit.guest');
Route::get('/public/{quiz}/{attemptId}/result/feedback', [QuizAttemptController::class, 'resultPublic'])->name('quiz.show.feedback');

Route::get('/public/quiz/{quiz}/attempt/{attempt}', [QuizAttemptController::class, 'showPublicAttempt'])
    ->name('quiz.attempt'); 


//members Login
Route::get('{organizationSlug?}/member/{groupSlug?}/register', [ExamineeAuthController::class, 'register'])
    ->name('organization.examinee.register');
Route::post('create/member/create/create', [ExamineeAuthController::class, 'create'])
    ->name('organization.examinee.create');

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

        // Categories
        Route::get('/categories', [LandlordHelpCenterController::class, 'categoriesIndex'])
            ->name('categories.index');
        Route::post('/categories', [LandlordHelpCenterController::class, 'storeCategory'])
            ->name('categories.store');
        Route::put('/categories/{category}', [LandlordHelpCenterController::class, 'updateCategory'])
            ->name('categories.update');
        Route::delete('/categories/{category}', [LandlordHelpCenterController::class, 'destroyCategory'])
            ->name('categories.destroy');
    
        // Articles
        Route::get('/articles', [LandlordHelpCenterController::class, 'articlesIndex'])
            ->name('articles.index');
        Route::post('/articles', [LandlordHelpCenterController::class, 'storeArticle'])
            ->name('articles.store');
        Route::put('/articles/{article}', [LandlordHelpCenterController::class, 'updateArticle'])
            ->name('articles.update');
        Route::delete('/articles/{article}', [LandlordHelpCenterController::class, 'destroyArticle'])
            ->name('articles.destroy');
       

    });

    //examiner
    Route::group([
        'prefix' => 'examiner',
        'middleware' => ['role:examiner','subscription'],
        'as' => 'examiner.',
    ], function () {
        
        Route::get('dashboard', [DashboardController::class, 'index'] )->name('dashboard');
        Route::resource('user', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        //Route::resource('groups', GroupController::class)->except(['edit'])
        //->scoped(['organization' => 'slug']);

        //groups
        Route::prefix('groups')->name('groups.')->group(function () {
            Route::resource('/', GroupController::class)->except(['edit'])
            ->scoped(['organization' => 'slug'])->parameters(['' => 'group']);
            Route::post('/add-members/{group}', [GroupController::class, 'addMembers'])->name('addMembers');
            Route::delete('/remove-member/{group}/{user}', [GroupController::class, 'removeMember'])->name('removeMember');
            Route::post('/assign-quizzes/{group}', [GroupController::class, 'assignQuizzes'])->name('assignQuizzes');
            Route::delete('/remove-quiz/{group}/{quiz}', [GroupController::class, 'removeQuiz'])->name('removeQuiz');
            Route::post('/{group}/move-users', [GroupController::class, 'moveUsers'])->name('moveUsers');
            //import users
            Route::post('/{group}/import-users', [GroupController::class, 'importUsers'])->name('importUsers');
            Route::get('/import-template', [GroupController::class, 'importTemplate'])->name('importTemplate');
        });

        Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
        Route::post('/onboarding/restart', [OnboardingController::class, 'restart'])->name('onboarding.restart');
        
        
        Route::resource('reports', ReportController::class);
        //settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/organization', [SettingController::class, 'updateOrganization'])->name('settings.organization.update');

        Route::resource('quizzes', QuizController::class);

        //designation
        Route::get('users/designations', [UserController::class, 'designations'])->name('users.designations');
        Route::post('users/designations', [UserController::class, 'storeDesignation'])->name('users.designations.store');

        //Add new question pool
        Route::get('pools/{pool}/questions', [QuestionController::class, 'create'])->name('pools.questions.create');
        Route::get('pools/{pool}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('pools.questions.edit');
        Route::put('pools/{pool}/questions/{question}/update', [QuestionController::class, 'update'])->name('pools.questions.update');
        Route::post('pools/{pool}/post/questions', [QuestionController::class, 'store'])->name('pools.questions.store');

        //addRemove pool to quiz
        Route::post('/quizzes/{quiz}/pools', [QuizController::class, 'attachPool'])
            ->name('quizzes.attach-pool');

        Route::delete('/quizzes/{quiz}/pools/{pool}', [QuizController::class, 'detachPool'])
            ->name('quizzes.detach-pool');

        //Quizzes CRUD
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
        Route::get('quizzes/{quiz}/attempts/{attempt}', [QuizController::class, 'showAttempts'])
            ->name('quiz.attempts.show');

        Route::resource('question-pools', QuestionPoolController::class);
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
            
        Route::get('quizzes/{quiz}/analysis/questions/{question}', [QuizAnalysisController::class, 'questionDetail'])
            ->name('quizzes.analysis.question');

        Route::get('quizzes/{quiz}/analysis/attempt/{attempt}', [QuizAnalysisController::class, 'quizAttempt'])
            ->name('quizzes.attempts.show');

        Route::post('/quizzes/{quiz}/analysis/export/{group}', [QuizAnalysisController::class, 'export'])
            ->name('quizzes.analysis.export');
        Route::get('quizzes/{quiz}/analysis/users/{user}', [QuizAnalysisController::class, 'byUser'])
            ->name('quizzes.analysis.user');
        
        //proctoring
        Route::get('/proctoring/violations/{attemptId}', [QuizAnalysisController::class, 'getViolations'])
            ->name('quizzes.analysis.violations');

        // Certificate Templates
        Route::resource('certificate-templates', CertificateTemplateController::class);
        Route::post('certificate-templates/{certificateTemplate}/set-default', [CertificateTemplateController::class, 'setDefault'])
            ->name('certificate-templates.set-default');

        Route::get('certificate-templates/{certificateTemplate}/preview', [CertificateTemplateController::class, 'preview'])
            ->name('certificate-templates.preview');

    
        //subcribe
        Route::get('/subscription', [PaymentController::class, 'index'])->name('subscription.plans');
        Route::post('/subscribe', [PaymentController::class, 'initializeSubscription'])->name('subscribe');
        Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');

        // Quiz Groups
        Route::get('quiz-groups', [QuizGroupController::class, 'index'])->name('quiz-groups.index');
        Route::get('quiz-groups/create', [QuizGroupController::class, 'create'])->name('quiz-groups.create');
        Route::post('quiz-groups', [QuizGroupController::class, 'store'])->name('quiz-groups.store');
        Route::get('quiz-groups/{quiz_group}', [QuizGroupController::class, 'show'])->name('quiz-groups.show');
        Route::get('quiz-groups/{quiz_group}/edit', [QuizGroupController::class, 'edit'])->name('quiz-groups.edit');
        Route::put('quiz-groups/{quiz_group}', [QuizGroupController::class, 'update'])->name('quiz-groups.update');
        Route::delete('quiz-groups/{quiz_group}', [QuizGroupController::class, 'destroy'])->name('quiz-groups.destroy');
        Route::post('/groups/{group}/send-invite', 
            [GroupController::class, 'sendInvite'])
            ->name('groups.sendInvite');

        // Grading Systems Management
        Route::get('grading-systems', [GradingSystemController::class, 'index'])->name('grading-systems.index');
        Route::get('grading-systems/create', [GradingSystemController::class, 'create'])->name('grading-systems.create');
        Route::post('grading-systems', [GradingSystemController::class, 'store'])->name('grading-systems.store');
        Route::get('grading-systems/{gradingSystem}/edit', [GradingSystemController::class, 'edit'])->name('grading-systems.edit');
        Route::put('grading-systems/{gradingSystem}', [GradingSystemController::class, 'update'])->name('grading-systems.update');
        Route::delete('grading-systems/{gradingSystem}', [GradingSystemController::class, 'destroy'])->name('grading-systems.destroy');
        Route::post('grading-systems/{gradingSystem}/set-default', [GradingSystemController::class, 'setDefault'])->name('grading-systems.set-default');

        // Quiz Group Items
        Route::post('quiz-groups/{quiz_group}/quizzes', [QuizGroupController::class, 'addQuiz'])
            ->name('quiz-groups.quizzes.store');
        Route::delete('quiz-groups/{quiz_group}/quizzes/{quiz}', [QuizGroupController::class, 'removeQuiz'])
            ->name('quiz-groups.quizzes.destroy');

        Route::get('/certificates/{certificate}/download', [ControllersCertificateController::class, 'download'])
            ->name('certificates.download');

        
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
        //save the time per question
        Route::post('/quiz/{quiz}/attempt/{attempt}/save-progress', [QuizAttemptController::class, 'saveProgress'])
            ->name('quiz.save-progress'); 
        //dashboard
        Route::get('dashboard', [ExamineeDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('history', [ExamineeDashboardController::class, 'history'])->name('history');
        Route::get('quizzes', [ExamineeDashboardController::class, 'quizzes'])->name('quizzes.index');

        //View and download certificates
        Route::get('/certificates', [ControllersCertificateController::class, 'index'])
        ->name('certificates.index');

        Route::get('/certificates/{certificate}', [ControllersCertificateController::class, 'show'])
            ->name('certificates.show');

        Route::get('/certificates/{certificate}/download', [ControllersCertificateController::class, 'download'])
            ->name('certificates.download');

        Route::get('/certificates/verify', [ControllersCertificateController::class, 'verify'])
            ->name('certificates.verify');
        
    });

    //instructor
    Route::group([
        'prefix' => 'instructor',
        'middleware' => 'role:examinee',
        'as' => 'instructor.',
    ], function () {


        Route::get('dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');
        //login
        
        
        
    });



});
