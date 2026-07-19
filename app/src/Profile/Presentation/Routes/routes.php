<?php

declare(strict_types=1);

use App\Profile\Presentation\Controller\PreviewProfileController;
use App\Profile\Presentation\Controller\SaveOnboardingController;
use App\Profile\Presentation\Controller\ChoosePrimaryProfilePhotoController;
use App\Profile\Presentation\Controller\ShowOnboardingController;
use App\Profile\Presentation\Controller\UpdateVisibilityController;
use App\Profile\Presentation\Controller\UploadProfilePhotoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(static function (): void {
    Route::get('/dating-profile/onboarding', ShowOnboardingController::class)
        ->name('dating-profile.onboarding.show');
    Route::post('/dating-profile/onboarding', SaveOnboardingController::class)
        ->name('dating-profile.onboarding.save');
    Route::get('/dating-profile/edit', ShowOnboardingController::class)
        ->name('dating-profile.edit');
    Route::post('/dating-profile/edit', SaveOnboardingController::class)
        ->name('dating-profile.update');
    Route::middleware('dating.profile')->group(static function (): void {
        Route::post('/dating-profile/photos', UploadProfilePhotoController::class)
            ->name('dating-profile.photos.upload');
        Route::post('/dating-profile/photos/{photo}/primary', ChoosePrimaryProfilePhotoController::class)
            ->whereNumber('photo')
            ->name('dating-profile.photos.primary');
    });
    Route::post('/dating-profile/visibility', UpdateVisibilityController::class)
        ->name('dating-profile.visibility.update');
    Route::get('/dating-profile/preview', PreviewProfileController::class)
        ->name('dating-profile.preview');
});
