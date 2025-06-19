<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\KindeService;

/**
 * AppServiceProvider - Main application service provider
 * 
 * This service provider handles application-wide bootstrapping and service registration.
 * It sets up global view data for authentication status and user information,
 * making this data available to all views without manual injection.
 * 
 * 
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services
     * 
     * 
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services
     * 
     * This method is called after all service providers have been registered.
     * It sets up a global view composer that shares authentication data with all views.
     * 
     * @return void
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
