<?php

declare(strict_types=1);

use App\Profile\Presentation\Controller\PreviewProfileController;
use App\Profile\Presentation\Controller\SaveOnboardingController;
use App\Profile\Presentation\Controller\ShowOnboardingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(static function (): void {
    Route::get('/dating-profile/onboarding', ShowOnboardingController::class)
        ->name('dating-profile.onboarding.show');
    Route::post('/dating-profile/onboarding', SaveOnboardingController::class)
        ->name('dating-profile.onboarding.save');
    Route::get('/dating-profile/preview', PreviewProfileController::class)
        ->name('dating-profile.preview');
});
