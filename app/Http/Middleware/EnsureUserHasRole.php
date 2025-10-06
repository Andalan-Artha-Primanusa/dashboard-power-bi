<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Pakai di route: ->middleware('role:gm,super_admin')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->deny($request, 401, 'Unauthenticated.');
        }

        // Normalisasi ke lowercase buat perbandingan yang konsisten
        $userRole  = strtolower($user->role ?? 'user');
        $needRoles = array_map(fn($r) => strtolower(trim($r)), $roles);

        // SUPER ADMIN override: selalu lolos
        if ($userRole === 'super_admin') {
            return $next($request);
        }

        // Cek membership role
        if (!in_array($userRole, $needRoles, true)) {
            return $this->deny($request, 403, 'You do not have the required role.');
        }

        return $next($request);
    }

    private function deny(Request $request, int $status, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }
        abort($status, $message);
    }
}
