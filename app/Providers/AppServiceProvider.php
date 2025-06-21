<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Configure CORS settings for Laravel's built-in HandleCors middleware
        // This is equivalent to what would be in config/cors.php if that file existed.
        $this->app->singleton('cors', function ($app) {
            return [
                'paths' => ['api/*', 'sanctum/csrf-cookie'], // Ensure your API paths and Sanctum's CSRF cookie endpoint are included
                'allowed_methods' => ['*'],
                'allowed_origins' => ['http://localhost:3000'], // <-- IMPORTANT: Your React frontend URL
                'allowed_origins_patterns' => [],
                'allowed_headers' => ['*'],
                'exposed_headers' => [],
                'max_age' => 0,
                'supports_credentials' => true, // <-- ESSENTIAL for Sanctum SPA authentication
            ];
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
