<?php

declare(strict_types=1);

use App\Auth\Presentation\Controller\LoginController;
use App\Auth\Presentation\Controller\LogoutController;
use App\Auth\Presentation\Controller\RegisterController;
use App\Auth\Presentation\Controller\ShowLoginFormController;
use App\Auth\Presentation\Controller\ShowRegisterFormController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(static function (): void {
    Route::get('/login', ShowLoginFormController::class)
        ->middleware('guest')
        ->name('auth.login.show-form');

    Route::post('/login', LoginController::class)
        ->middleware('guest')
        ->name('auth.login');

    Route::get('/register', ShowRegisterFormController::class)
        ->middleware('guest')
        ->name('auth.register.show-form');

    Route::post('/register', RegisterController::class)
        ->middleware('guest')
        ->name('auth.register');

    Route::post('/logout', LogoutController::class)
        ->middleware('auth')
        ->name('auth.logout');

    Route::get('/logout', LogoutController::class)
        ->middleware('auth')
        ->name('auth.logout.get');
});
