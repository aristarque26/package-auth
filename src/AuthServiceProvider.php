<?php

namespace Taibi\AuthAPI;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Taibi\AuthAPI\Middleware\RoleMiddleware;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Enregistrement des services du package.
     */
    public function register(): void
    {
        // Fusion de la config
        $this->mergeConfigFrom(
            __DIR__.'/../config/auth-api.php', 'auth-api'
        );
    }

    /**
     * Bootstrap des services du package.
     */
    public function boot(): void
    {
        // 1. Chargement des routes API
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // 2. Chargement des migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // 3. Publication de la config
        $this->publishes([
            __DIR__.'/../config/auth-api.php' => config_path('auth-api.php'),
        ], 'auth-api-config');

        // 4. Publication des migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'auth-api-migrations');

        // 5. Enregistrement de l'alias du middleware 'role'
        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('role', RoleMiddleware::class);
    }
}