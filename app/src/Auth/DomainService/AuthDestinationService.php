<?php

declare(strict_types=1);

namespace App\Auth\DomainService;

use App\Models\User;

final readonly class AuthDestinationService
{
    public function routeNameFor(User $user): string
    {
        if (!$user->hasActiveAccount()) {
            return 'auth.account-unavailable';
        }

        if (!$user->hasVerifiedEmail()) {
            return 'verification.notice';
        }

        if (null === $user->datingProfile()->first()) {
            return 'auth.welcome';
        }

        return 'discovery.show';
    }
}
