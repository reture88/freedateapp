<?php

declare(strict_types=1);

use App\Discovery\Presentation\Controller\ShowDiscoveryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(static function (): void {
    Route::get('/discovery', ShowDiscoveryController::class)
        ->name('discovery.show');
});
