<?php

use Illuminate\Support\Facades\Route;
use Taibi\AuthAPI\Controllers\AuthController;
use Taibi\AuthAPI\Controllers\EmailVerificationController;
use Taibi\AuthAPI\Controllers\PasswordResetController;
use Taibi\AuthAPI\Controllers\UserManagementController;

Route::prefix('api/auth')->group(function () {

    // === ROUTES PUBLIQUES ===
    Route::get('/test', function () {
        return response()->json(['message' => 'API fonctionne !', 'status' => 'success']);
    });

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/email/verify', [EmailVerificationController::class, 'sendVerification']);
    Route::post('/email/verify/confirm', [EmailVerificationController::class, 'verify']);

    Route::post('/password/forgot', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

    // === PROTÉGÉES ===
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // === ADMIN + SUPER ADMIN ===
    Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->group(function () {
        Route::get('/admin/users', [UserManagementController::class, 'index']);
        Route::get('/admin/users/{id}', [UserManagementController::class, 'show']);
        Route::put('/admin/users/{id}', [UserManagementController::class, 'update']);
        Route::delete('/admin/users/{id}', [UserManagementController::class, 'destroy']);
        Route::put('/admin/users/{id}/toggle-status', [UserManagementController::class, 'toggleStatus']);
    });

    // === SUPER ADMIN ===
    Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
        Route::post('/super-admin/users', [UserManagementController::class, 'store']);
        Route::post('/super-admin/users/admin', [UserManagementController::class, 'storeAdmin']);
        Route::post('/super-admin/users/personnel', [UserManagementController::class, 'storePersonnel']);
    });

    // === ADMIN ===
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/admin/users/personnel', [UserManagementController::class, 'storePersonnel']);
    });

    // === PERSONNEL ===
    Route::middleware(['auth:sanctum', 'role:personnel'])->group(function () {
        Route::get('/personnel/dashboard', function () {
            return response()->json(['message' => 'Dashboard du personnel', 'tasks' => ['Tâche 1', 'Tâche 2']]);
        });
    });

    // === CLIENT ===
    Route::middleware(['auth:sanctum', 'role:client'])->group(function () {
        Route::get('/client/dashboard', function () {
            return response()->json(['message' => 'Dashboard du client', 'orders' => ['Commande 1', 'Commande 2']]);
        });
    });

});