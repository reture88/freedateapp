<?php

declare(strict_types=1);

use App\User\Presentation\Controller\ChangePasswordController;
use App\User\Presentation\Controller\DeactivateAccountController;
use App\User\Presentation\Controller\ProfileController;
use App\User\Presentation\Controller\RequestAccountDeletionController;
use App\User\Presentation\Controller\SaveAddressController;
use App\User\Presentation\Controller\UpdateProfileController;
use App\User\Presentation\Controller\Admin\AdminUserIndexController;
use App\User\Presentation\Controller\Admin\AdminUserShowController;
use App\User\Presentation\Controller\Admin\AdminUserUpdateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(static function (): void {
    Route::get('/profile', ProfileController::class)->name('profile.show');
    Route::post('/profile', UpdateProfileController::class)->name('profile.update');
    Route::post('/profile/password', ChangePasswordController::class)->name('profile.password.update');
    Route::post('/profile/addresses/{type}', SaveAddressController::class)
        ->whereIn('type', ['shipping', 'billing'])
        ->name('profile.address.save');
    Route::post('/account/deactivate', DeactivateAccountController::class)->name('account.deactivate');
    Route::post('/account/request-deletion', RequestAccountDeletionController::class)->name('account.request-deletion');
});

Route::prefix('admin/users')
    ->middleware(['auth', 'admin'])
    ->group(static function (): void {
        Route::get('/', AdminUserIndexController::class)->name('admin.users.index');
        Route::get('/{user}', AdminUserShowController::class)->name('admin.users.show');
        Route::post('/{user}', AdminUserUpdateController::class)->name('admin.users.update');
    });
