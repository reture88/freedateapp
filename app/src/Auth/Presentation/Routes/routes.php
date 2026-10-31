<?php

declare(strict_types=1);

use App\Auth\Presentation\Controller\LoginController;
use App\Auth\Presentation\Controller\LogoutController;
use App\Auth\Presentation\Controller\RegisterController;
use App\Auth\Presentation\Controller\ResendEmailVerificationController;
use App\Auth\Presentation\Controller\ResetPasswordController;
use App\Auth\Presentation\Controller\SendPasswordResetLinkController;
use App\Auth\Presentation\Controller\ShowAccountUnavailableController;
use App\Auth\Presentation\Controller\ShowEmailVerificationPromptController;
use App\Auth\Presentation\Controller\ShowExpiredResetLinkController;
use App\Auth\Presentation\Controller\ShowForgotPasswordController;
use App\Auth\Presentation\Controller\ShowLoginFormController;
use App\Auth\Presentation\Controller\ShowRegisterFormController;
use App\Auth\Presentation\Controller\ShowResetPasswordController;
use App\Auth\Presentation\Controller\ShowWelcomeController;
use App\Auth\Presentation\Controller\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(static function (): void {
    Route::get('/login', ShowLoginFormController::class)
        ->middleware('guest')
        ->name('auth.login.show-form');

    Route::post('/login', LoginController::class)
        ->middleware(['guest', 'throttle:10,1'])
        ->name('auth.login');

    Route::get('/register', ShowRegisterFormController::class)
        ->middleware('guest')
        ->name('auth.register.show-form');

    Route::post('/register', RegisterController::class)
        ->middleware(['guest', 'throttle:8,1'])
        ->name('auth.register');

    Route::get('/welcome', ShowWelcomeController::class)
        ->middleware(['auth', 'account.active', 'verified'])
        ->name('auth.welcome');

    Route::get('/account-unavailable', ShowAccountUnavailableController::class)
        ->name('auth.account-unavailable');

    Route::get('/verify-email', ShowEmailVerificationPromptController::class)
        ->middleware(['auth', 'account.active'])
        ->name('verification.notice');

    Route::post('/email/verification-notification', ResendEmailVerificationController::class)
        ->middleware(['auth', 'account.active', 'throttle:6,1'])
        ->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['auth', 'account.active', 'signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/logout', LogoutController::class)
        ->middleware('auth')
        ->name('auth.logout');

    Route::get('/logout', LogoutController::class)
        ->middleware('auth')
        ->name('auth.logout.get');
});

Route::middleware('guest')->group(static function (): void {
    Route::get('/forgot-password', ShowForgotPasswordController::class)
        ->name('password.request');
    Route::post('/forgot-password', SendPasswordResetLinkController::class)
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/expired', ShowExpiredResetLinkController::class)
        ->name('password.expired');
    Route::get('/reset-password/{token}', ShowResetPasswordController::class)
        ->name('password.reset');
    Route::post('/reset-password', ResetPasswordController::class)
        ->middleware('throttle:5,1')
        ->name('password.update');
});
