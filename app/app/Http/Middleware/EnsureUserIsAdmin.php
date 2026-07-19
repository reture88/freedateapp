<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserIsAdmin
{
    /**
     * @param \Closure(Request): Response $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $user = $request->user();

        if (null === $user || !$user->canAccessAdminPanel()) {
            abort(403);
        }

        return $next($request);
    }
}
