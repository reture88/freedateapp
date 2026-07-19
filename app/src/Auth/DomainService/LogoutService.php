<?php

declare(strict_types=1);

namespace App\Auth\DomainService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final readonly class LogoutService
{
    public function handle(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
