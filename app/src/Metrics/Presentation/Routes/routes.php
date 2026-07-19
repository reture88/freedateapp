<?php

declare(strict_types=1);

use App\Metrics\Presentation\Controller\AdminMetricsDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(static function (): void {
    Route::get('/admin/metrics', AdminMetricsDashboardController::class)
        ->name('admin.metrics.index');
});
