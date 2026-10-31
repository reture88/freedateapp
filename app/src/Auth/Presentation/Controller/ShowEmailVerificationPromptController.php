<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Controller;

use App\Auth\DomainService\AuthDestinationService;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class ShowEmailVerificationPromptController
{
    public function __construct(private AuthDestinationService $destinationService)
    {
    }

    public function __invoke(Request $request): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user->hasActiveAccount()) {
            return redirect()->route('auth.account-unavailable');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route($this->destinationService->routeNameFor($user));
        }

        return view('auth.verify-email', ['user' => $user]);
    }
}
