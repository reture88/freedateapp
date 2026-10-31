<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Auth\Presentation\Controller\HomeController;

Route::get('/', HomeController::class)->name('home');
