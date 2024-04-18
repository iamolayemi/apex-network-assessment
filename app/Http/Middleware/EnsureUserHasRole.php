<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     */
        public function handle(Request $request, Closure $next, string $role): Response
    {
        if ($request->user()->role !== UserRole::tryFrom($role)) {
            abort(403, 'You are not authorized to access this resource.');
        }

        return $next($request);
    }
}
