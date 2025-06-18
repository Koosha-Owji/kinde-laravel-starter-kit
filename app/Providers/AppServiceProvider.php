<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\KindeService;

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
        // Share authentication data with all views
        View::composer('*', function ($view) {
            $kindeService = app(KindeService::class);
            $isAuthenticated = $kindeService->isAuthenticated();
            
            $view->with([
                'isAuthenticated' => $isAuthenticated,
                'authUser' => $isAuthenticated ? $kindeService->getUser() : null,
            ]);
        });
    }
}
