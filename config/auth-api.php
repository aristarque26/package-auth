<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration du package Taibi Auth API
    |--------------------------------------------------------------------------
    */

    // Modèle User utilisé
    'user_model' => \App\Models\User::class,

    // Vérification email activée
    'verify_email' => env('AUTH_VERIFY_EMAIL', true),

    // Durée de vie de l'OTP (en minutes)
    'otp_expiration' => env('AUTH_OTP_EXPIRATION', 30),

    // Durée de vie du token (en minutes)
    'token_expiration' => env('AUTH_TOKEN_EXPIRATION', 1440),

    // Préfixe des routes
    'route_prefix' => env('AUTH_ROUTE_PREFIX', 'api/auth'),

    // Middleware par défaut
    'middleware' => ['api'],
];