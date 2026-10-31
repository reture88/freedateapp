<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureAccountIsActive
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

        return $next($request);
    }
}
