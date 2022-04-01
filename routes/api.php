<?php

use App\Http\Controllers\Api\PagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(PagesController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('health', 'health');
});

Route::group(['prefix' => 'auth'], function () {
    // Route::post('invite', [UserController::class, 'invite']);
    // Route::post('login', LoginController::class);
    // Route::post('register', RegistrationController::class);
    // Route::post('logout', LogoutController::class);
    // Route::post('verify', [AccountVerificationController::class, 'verifyAccount']);
    // Route::post('forgot-password', [PasswordController::class, 'forgotPassword']);
    // Route::post('reset-password', [PasswordController::class, 'resetPassword']);
});

Route::fallback(fn () => response()->not_found());
