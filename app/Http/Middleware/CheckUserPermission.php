<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserPermission
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (!Auth::check()) {
            abort(401, 'Unauthenticated.');
        }

        $user = Auth::user();

        // User active
        if (!$user->is_active) {
            abort(403, 'Your account is inactive.');
        }

        // User type exists
        if (!$user->userType) {
            abort(403, 'User role not found.');
        }

        // Role active
        if (!$user->userType->is_active) {
            abort(403, 'User role is inactive.');
        }

        /*
        |--------------------------------------------------------------------------
        | Check Permission
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permission) {

            if (hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'You do not have permission to access this resource.');
    }
}