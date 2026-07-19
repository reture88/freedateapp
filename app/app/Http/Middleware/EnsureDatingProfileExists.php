<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
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

        if (null === $user || null === $user->datingProfile()->first()) {
            return redirect('/dating-profile/onboarding')
                ->with('warning', 'Complete your dating profile before using this feature.');
        }

        return $next($request);
    }
}
