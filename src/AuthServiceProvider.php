<?php

namespace Taibi\AuthAPI;

use Illuminate\Support\ServiceProvider;

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
        // Chargement des routes API
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // Chargement des migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publication de la config
        $this->publishes([
            __DIR__.'/../config/auth-api.php' => config_path('auth-api.php'),
        ], 'auth-api-config');
    }
}