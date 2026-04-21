<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user()) {
            return redirect('login');
        }

        // Support both old enum-based role and new RBAC role system
        if ($request->user()->role === $role || $request->user()->hasRole($role)) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
