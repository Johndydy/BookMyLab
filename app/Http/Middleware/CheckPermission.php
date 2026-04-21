<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Usage in routes: middleware('permission:approve-booking')
     * Multiple permissions: middleware('permission:approve-booking,reject-booking')
     */
    public function handle(Request $request, Closure $next, string ...$permissions)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Collect all permissions across all of the user's roles
        $userPermissions = $user->roles
            ->flatMap->permissions
            ->pluck('name')
            ->unique()
            ->toArray();

        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Unauthorized. You do not have the required permission.',
        ], 403);
    }
}