<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration du package Taibi Auth API
    |--------------------------------------------------------------------------
    */

    // Préfixe des routes (ex: api/auth)
    'route_prefix' => 'api/auth',

    // Middleware par défaut
    'middleware' => ['api'],

    // Modèle User utilisé
    'user_model' => \App\Models\User::class,

    // Durée de vie du token (en jours)
    'token_expiration_days' => 7,

    // Activer l'OTP
    'otp_enabled' => false,

    // Activer la vérification email
    'email_verification_enabled' => false,
];