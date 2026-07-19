<?php

declare(strict_types=1);

use App\Connection\Presentation\Controller\ListMatchesController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(static function (): void {
    Route::get('/matches', ListMatchesController::class)
        ->name('matches.index');
});
