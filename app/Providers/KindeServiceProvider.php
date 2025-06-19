<?php

namespace App\Providers;

use App\Services\KindeService;
use App\Http\Middleware\KindeAuth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

/**
 * KindeServiceProvider - Service provider for Kinde authentication integration
 * 
 * This service provider handles the registration and configuration of Kinde authentication
 * services within the Laravel application.
 * 
 * @package App\Providers
 */
class KindeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services
     * 
     * This method registers the KindeService as a singleton in Laravel's service container.
     * Using singleton ensures that the same instance is used throughout the request lifecycle,
     * which is important for maintaining authentication state and avoiding unnecessary
     * re-initialization of the Kinde PHP SDK.
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(KindeService::class, function ($app) {
            return new KindeService();
        });
    }

    /**
     * Bootstrap any application services
     * 
     * This method is called after all providers have been registered and performs
     * the following bootstrap operations:
     * 1. Registers the 'kinde.auth' middleware alias for route protection
     * 2. Validates that all required Kinde configuration is present
     * @return void
     * @throws \Exception If required Kinde configuration is missing
     */
    public function boot(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('kinde.auth', KindeAuth::class);

        $this->validateKindeConfig();
    }

    /**
     * Validate that required Kinde configuration is present
     * 
     * This method checks that all required configuration keys are present and not empty.
     * 
     * 
     * @return void
     * @throws \Exception If any required configuration key is missing or empty
     */
    protected function validateKindeConfig(): void
    {
        $requiredKeys = [
            'services.kinde.domain',
            'services.kinde.client_id',
            'services.kinde.client_secret',
            'services.kinde.redirect_url',
            'services.kinde.post_logout_redirect_url',
        ];

        foreach ($requiredKeys as $key) {
            if (empty(config($key))) {
                throw new \Exception("Kinde configuration missing: {$key}");
            }
        }
    }
} 