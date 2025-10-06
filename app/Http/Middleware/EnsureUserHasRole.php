<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Gunakan di route: ->middleware('role:gm,super_admin')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        // cek role user, default ke 'user' kalau null
        if (!in_array($user->role ?? 'user', $roles, true)) {
            abort(403, 'You do not have the required role.');
        }

        return $next($request);
    }
}
