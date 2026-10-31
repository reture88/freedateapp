<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Controller;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;

final class ResetPasswordController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $validated,
            static function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if (Password::PASSWORD_RESET !== $status) {
            return redirect()->route('password.expired')
                ->with('error', 'That reset link is invalid or has expired. Request a new link to continue.');
        }

        return redirect()->route('auth.login.show-form')
            ->with('status', 'Your password has been updated. Log in to continue.');
    }
}
