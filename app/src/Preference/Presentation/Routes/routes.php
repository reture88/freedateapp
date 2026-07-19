<?php

declare(strict_types=1);

use App\Preference\Presentation\Controller\EditPreferenceController;
use App\Preference\Presentation\Controller\SavePreferenceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(static function (): void {
    Route::get('/dating-preferences/edit', EditPreferenceController::class)
        ->name('dating-preferences.edit');
    Route::post('/dating-preferences', SavePreferenceController::class)
        ->name('dating-preferences.save');
});
