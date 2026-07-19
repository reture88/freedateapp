<?php

declare(strict_types=1);

use App\Connection\Presentation\Controller\ListMatchesController;
use App\Connection\Presentation\Controller\UnmatchConnectionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(static function (): void {
    Route::get('/matches', ListMatchesController::class)
        ->name('matches.index');
    Route::post('/connections/{connection}/unmatch', UnmatchConnectionController::class)
        ->whereNumber('connection')
        ->name('connections.unmatch');
});
