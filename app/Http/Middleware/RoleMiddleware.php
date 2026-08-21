<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {

        // Get authenticated user using the web guard
        $user = $request->user('web');

        // Not logged in
        if (!$user) {
            return redirect()
                ->route('login')
                ->with('error', 'Please login to continue.');
        }

        // Get user's role name safely from relationship
        $userRole = strtolower(trim((string) ($user->role?->name ?? '')));

        // Normalize allowed roles from route
        $allowedRoles = array_map(
            fn ($role) => strtolower(trim($role)),
            $roles
        );

        // Check permission
        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}