<?php

declare(strict_types=1);

use App\Safety\Presentation\Controller\BlockUserController;
use App\Safety\Presentation\Controller\Admin\AdminReportIndexController;
use App\Safety\Presentation\Controller\Admin\AdminReportShowController;
use App\Safety\Presentation\Controller\SubmitReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(static function (): void {
    Route::post('/reports', SubmitReportController::class)
        ->name('reports.submit');
    Route::post('/blocks', BlockUserController::class)
        ->name('blocks.submit');
});

Route::prefix('admin/reports')
    ->middleware(['auth', 'admin'])
    ->group(static function (): void {
        Route::get('/', AdminReportIndexController::class)->name('admin.reports.index');
        Route::get('/{report}', AdminReportShowController::class)->name('admin.reports.show');
    });
