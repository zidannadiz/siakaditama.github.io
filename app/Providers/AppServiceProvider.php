<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        // Share notification counts with all views
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $unreadNotifikasis = $user->notifikasis()->where('is_read', false)->get();
                $unreadCount = $unreadNotifikasis->count();
                $recentNotifikasis = $unreadNotifikasis->sortByDesc('created_at')->take(5);
                
                $view->with([
                    'unreadCount' => $unreadCount,
                    'recentNotifikasis' => $recentNotifikasis,
                ]);
            }
        });

        View::composer('layouts.sidebar', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $unreadNotifikasis = $user->notifikasis()->where('is_read', false)->get();
                $unreadNotifCount = $unreadNotifikasis->count();
                
                $pendingKrsCount = 0;
                if ($user->role === 'admin') {
                    $pendingKrsCount = \App\Models\KRS::where('status', 'pending')->count();
                }
                
                $view->with([
                    'unreadNotifCount' => $unreadNotifCount,
                    'pendingKrsCount' => $pendingKrsCount,
                ]);
            }
        });
    }
}
