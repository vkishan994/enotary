<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer([
            'front.layouts.dashboard.header',
            'front.layouts.logged-in-header',
            'admin.layouts.header',
        ], function ($view) {

            if (Auth::check()) {
                $user = Auth::user();

                $notifications = $user->unreadNotifications()
                    ->latest()
                    ->take(10)
                    ->get();

                $unreadCount = $user->unreadNotifications()->count();

                $view->with([
                    'notifications' => $notifications,
                    'unreadCount'   => $unreadCount,
                ]);
            } else {
                $view->with([
                    'notifications' => collect(),
                    'unreadCount'   => 0,
                ]);
            }
        });
    }
}
