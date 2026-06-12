<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDemoPaymentController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GigController;
use App\Http\Controllers\GigOrderController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WorkSubmissionController;
use App\Models\GigCategory;
use App\Models\Gig;
use App\Models\GigServiceType;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    $gigCategories = GigCategory::orderBy('name')->get();
    $popularTags = GigServiceType::orderBy('name')->limit(4)->pluck('name')->toArray();
    $featuredGigs = Gig::with('freelancer')
        ->where('status', 'active')
        ->latest()
        ->take(6)
        ->get();
    $featuredJobs = Job::with('employer')
        ->where('status', 'open')
        ->whereDate('deadline', '>=', now()->toDateString())
        ->latest()
        ->take(6)
        ->get();
    if (empty($popularTags)) {
        $popularTags = $gigCategories->take(4)->pluck('name')->toArray();
    }
    return view('index', compact('gigCategories', 'popularTags', 'featuredGigs', 'featuredJobs'));
});
Route::get('/about', function () { return view('about2'); });
Route::get('/contact', function () { return view('contact2'); });
Route::get('/terms', function () { return view('term-of-use'); });

Route::middleware(['auth'])->group(function () {
    Broadcast::routes();

    Route::get('/dashboard', function () {
        $user = Auth::user();
        return $user->role === 'candidate'
            ? redirect()->route('candidate.dashboard')
            : redirect()->route('employer.dashboard');
    })->name('dashboard');

    Route::middleware(['can:is-candidate'])->prefix('candidate')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'candidate'])->name('candidate.dashboard');
        Route::get('/orders', [GigOrderController::class, 'candidateOrders'])->name('candidate.orders');

        Route::get('/my-services', [GigController::class, 'myGigs'])->name('candidate.services');
        Route::get('/gigs/create', [GigController::class, 'create'])->name('gigs.create');
        Route::post('/gigs', [GigController::class, 'store'])->name('gigs.store');
        Route::get('/gigs/{gig:slug}/edit', [GigController::class, 'edit'])->name('gigs.edit');
        Route::put('/gigs/{gig:slug}', [GigController::class, 'update'])->name('gigs.update');
        Route::patch('/gigs/{gig:slug}/status', [GigController::class, 'updateStatus'])->name('gigs.status');

        Route::get('/proposals', [ProposalController::class, 'myProposals'])->name('candidate.proposals');
    });

    Route::middleware(['can:is-employer'])->prefix('employer')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'employer'])->name('employer.dashboard');

        Route::get('/jobs', [JobController::class, 'employerIndex'])->name('employer.jobs.index');
        Route::get('/jobs/create', [JobController::class, 'create'])->name('employer.jobs.create');
        Route::post('/jobs', [JobController::class, 'store'])->name('employer.jobs.store');
        Route::get('/jobs/{job:slug}', [JobController::class, 'employerShow'])->name('employer.jobs.show');
        Route::get('/jobs/{job:slug}/edit', [JobController::class, 'edit'])->name('employer.jobs.edit');
        Route::put('/jobs/{job:slug}', [JobController::class, 'update'])->name('employer.jobs.update');
        Route::delete('/jobs/{job:slug}', [JobController::class, 'destroy'])->name('employer.jobs.destroy');
        Route::patch('/jobs/{job:slug}/status', [JobController::class, 'updateStatus'])->name('employer.jobs.updateStatus');

        Route::get('/applicants', [ProposalController::class, 'employerApplicants'])->name('employer.applicants');
    });

    Route::post('/jobs/{job:slug}/proposals', [ProposalController::class, 'store'])->name('proposals.store');
    Route::patch('/proposals/{proposal:slug}', [ProposalController::class, 'update'])->name('proposals.update');
    Route::delete('/proposals/{proposal:slug}', [ProposalController::class, 'destroy'])->name('proposals.destroy');
    Route::post('/proposals/{proposal:slug}/accept', [ProposalController::class, 'accept'])->name('proposals.accept');
    Route::patch('/proposals/{proposal:slug}/reject', [ProposalController::class, 'reject'])->name('proposals.reject');
    Route::post('/jobs/{job:slug}/submissions', [WorkSubmissionController::class, 'store'])->name('submissions.store');
    Route::patch('/submissions/{submission:slug}/status', [WorkSubmissionController::class, 'updateStatus'])->name('submissions.updateStatus');
    Route::post('/payments/{job:slug}/release', [PaymentController::class, 'release'])->name('payments.release');

    Route::post('/gig/{gig:slug}/order', [GigOrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order:slug}/status', [GigOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::get('/messages', [ConversationController::class, 'index'])->name('messages.index');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/messages/{conversation:slug}', [ConversationController::class, 'show'])->name('messages.show');
    Route::get('/messages/{conversation:slug}/latest', [MessageController::class, 'latest'])->name('messages.latest');
    Route::post('/messages/{conversation:slug}', [MessageController::class, 'store'])->name('messages.store');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification:slug}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

Route::get('/jobs', [JobController::class, 'publicIndex'])->name('jobs.index');
Route::get('/job/{job:slug}', [JobController::class, 'publicShow'])->name('jobs.show');
Route::get('/services', [GigController::class, 'index'])->name('services.index');
Route::get('/gig/{gig:slug}', [GigController::class, 'show'])->name('services.show');
Route::get('/categories', [GigController::class, 'categories'])->name('gig.categories');
Route::get('/subcategories/{category:slug}', [GigController::class, 'subcategories'])->name('gig.subcategories');
Route::get('/services/{subcategory:slug}', [GigController::class, 'services'])->name('gig.services');

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    Route::middleware([\App\Http\Middleware\AdminAuthenticated::class])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::get('/profile/setup', [AdminProfileController::class, 'setupForm'])->name('admin.profile.setup');
        Route::post('/profile/setup', [AdminProfileController::class, 'saveSetup'])->name('admin.profile.setup.save');

        Route::middleware([\App\Http\Middleware\AdminProfileCompleted::class])->group(function () {
            Route::get('/dashboard', function () {
                $email = (string) session('admin_auth.email');
                $profile = \App\Models\AdminProfile::where('email', $email)->first();
                return view('admin.dashboard', compact('profile', 'email'));
            })->name('admin.dashboard');

            Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
            Route::get('/users/{user:slug}', [AdminUserController::class, 'show'])->name('admin.users.show');
            Route::get('/users/{user:slug}/gigs/{gig:slug}', [AdminUserController::class, 'showGig'])->name('admin.users.gigs.show');
            Route::get('/users/{user:slug}/jobs/{job:slug}', [AdminUserController::class, 'showJob'])->name('admin.users.jobs.show');

            Route::get('/demo-payments', [AdminDemoPaymentController::class, 'index'])->name('admin.demo.payments');
            Route::post('/demo-payments/{order:slug}/verify', [AdminDemoPaymentController::class, 'verify'])->name('admin.demo.payments.verify');
        });
    });
});

require __DIR__.'/auth.php';

Route::fallback(function () { return view('error-404'); });
