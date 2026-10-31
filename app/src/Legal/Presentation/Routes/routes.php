<?php

declare(strict_types=1);

use App\Legal\Presentation\Controller\ShowPrivacyController;
use App\Legal\Presentation\Controller\ShowTermsController;
use Illuminate\Support\Facades\Route;

Route::get('/terms-of-service', ShowTermsController::class)->name('legal.terms');
Route::get('/privacy-policy', ShowPrivacyController::class)->name('legal.privacy');
