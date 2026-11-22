<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Authentication & User Management API endpoints
|
*/

Route::prefix('auth')->group(function () {
    // Public routes (Rate limited)
    Route::middleware('throttle:60,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
        Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.auth.forgot-password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.auth.reset-password');
        Route::get('/reset-password/{token}', function (string $token) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset token received. Submit POST /api/auth/reset-password to complete reset.',
                'data' => [
                    'token' => $token,
                    'email' => request()->query('email'),
                ],
            ]);
        })->name('password.reset');
    });

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('api.auth.update-profile');
        Route::put('/change-password', [AuthController::class, 'changePassword'])->name('api.auth.change-password');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('api.auth.logout-all');
    });
});
