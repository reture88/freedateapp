<?php

declare(strict_types=1);

use App\Discovery\Presentation\Controller\AcceptDiscoveryController;
use App\Discovery\Presentation\Controller\RejectDiscoveryController;
use App\Discovery\Presentation\Controller\ShowDiscoveryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(static function (): void {
    Route::get('/discovery', ShowDiscoveryController::class)
        ->name('discovery.show');
    Route::post('/discovery/{targetProfile}/accept', AcceptDiscoveryController::class)
        ->whereNumber('targetProfile')
        ->name('discovery.accept');
    Route::post('/discovery/{targetProfile}/reject', RejectDiscoveryController::class)
        ->whereNumber('targetProfile')
        ->name('discovery.reject');
});
