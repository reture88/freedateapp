<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Controller;

use App\Auth\DomainService\AuthDestinationService;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

final readonly class VerifyEmailController
{
    public function __construct(private AuthDestinationService $destinationService)
    {
    }

    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user->hasActiveAccount()) {
            return redirect()->route('auth.account-unavailable');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return redirect()->route($this->destinationService->routeNameFor($user))
            ->with('success', 'Email verified.');
    }
}
