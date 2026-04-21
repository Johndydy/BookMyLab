<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Usage in routes: middleware('role:administrator')
     * Multiple roles: middleware('role:administrator,staff')
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        // Must be authenticated first
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Unauthorized. You do not have permission to access this resource.',
        ], 403);
    }
}