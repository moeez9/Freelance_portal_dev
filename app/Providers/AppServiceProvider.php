<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use App\Models\Job;
use App\Models\ConversationParticipant;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (!$this->app->runningInConsole()) {
                Job::closeExpiredOpenJobs();
            }
        } catch (\Throwable $exception) {
            // Skip automatic sync when DB is not ready (e.g., during setup/migrations).
        }

        Gate::define('is-candidate', function (User $user) {
            return $user->role === 'candidate';
        });

        Gate::define('is-employer', function (User $user) {
            return $user->role === 'employer';
        });

        Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
            return ConversationParticipant::where('conversation_id', $conversationId)
                ->where('user_id', $user->id)
                ->exists();
        });

        View::composer(['layouts.header'], function ($view) {
            $notifications = collect();
            $unreadCount = 0;

            if (Auth::check()) {
                $notifications = UserNotification::where('user_id', Auth::id())
                    ->latest()
                    ->take(5)
                    ->get();
                $unreadCount = UserNotification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->count();
            }

            $view->with('headerNotifications', $notifications);
            $view->with('headerUnreadNotificationCount', $unreadCount);
        });
    }
}
