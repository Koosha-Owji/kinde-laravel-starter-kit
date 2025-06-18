<?php

namespace App\Providers;

use App\Services\KindeService;
use App\Http\Middleware\KindeAuth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class KindeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register KindeService as a singleton
        $this->app->singleton(KindeService::class, function ($app) {
            return new KindeService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register middleware
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('kinde.auth', KindeAuth::class);

        // Validate Kinde configuration
        $this->validateKindeConfig();
    }

    /**
     * Validate that required Kinde configuration is present
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