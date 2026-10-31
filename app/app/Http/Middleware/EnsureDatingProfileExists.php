<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Profile\Support\AdultEligibility;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureDatingProfileExists
{
    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user instanceof User) {
            return redirect()->route('auth.login.show-form');
        }

        if (!$user->hasActiveAccount()) {
            return redirect()->route('auth.account-unavailable');
        }

        $profile = $user->datingProfile()->first();

        if (null === $profile) {
            return redirect('/dating-profile/onboarding')
                ->with('warning', 'Complete your dating profile before using this feature.');
        }

        if (!$profile->hasAdultBirthdate()) {
            return redirect('/dating-profile/onboarding')
                ->withErrors(['birthdate' => AdultEligibility::MESSAGE]);
        }

        return $next($request);
    }
}
