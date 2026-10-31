<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

final class SendPasswordResetLinkController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        Password::sendResetLink($validated);

        return back()
            ->withInput($request->only('email'))
            ->with('status', 'If an account exists for this email, password reset instructions will be sent.');
    }
}
