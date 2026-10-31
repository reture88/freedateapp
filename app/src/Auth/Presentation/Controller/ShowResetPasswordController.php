<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Controller;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ShowResetPasswordController
{
    public function __invoke(Request $request, string $token): View
    {
        return view('auth.passwords.reset', [
            'email' => (string) $request->query('email', ''),
            'token' => $token,
        ]);
    }
}
