<?php

declare(strict_types=1);

use App\Safety\Presentation\Controller\SubmitReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(static function (): void {
    Route::post('/reports', SubmitReportController::class)
        ->name('reports.submit');
});
