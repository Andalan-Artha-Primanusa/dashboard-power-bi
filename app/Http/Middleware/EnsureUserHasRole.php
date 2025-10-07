<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Pakai di route:
     *   ->middleware('role:gm|super_admin')
     *   atau
     *   ->middleware('role:gm,super_admin')
     */
    public function handle(Request $request, Closure $next, ...$params): Response
    {
        $user = $request->user();
        if (!$user) {
            return $this->deny($request, 401, 'Unauthenticated.');
        }

        // --- Ambil role user dari relasi/field string, lalu normalisasi ---
        $raw = $user->role->key
            ?? $user->role->slug
            ?? $user->role->name
            ?? (is_string($user->role ?? null) ? $user->role : '')
            ?? '';

        $norm = str($raw)->lower()->replace(['_', '-'], ' ')->squish()->toString();

        // Mapping variasi penamaan → kunci konsisten
        $map = [
            'general manager' => 'gm',
            'generalmanager'  => 'gm',
            'mgr'             => 'manager',
            'super admin'     => 'super_admin',
            'superadmin'      => 'super_admin',
            'sa'              => 'super_admin',
            'root'            => 'super_admin',
        ];
        $userRole = $map[$norm] ?? $norm;

        // --- Parse parameter: dukung "gm|super_admin" atau "gm,super_admin" ---
        // $params dari Laravel bisa seperti ["gm|super_admin"] atau ["gm","super_admin"]
        $allowed = collect($params)
            ->flatMap(function ($p) {
                // pecah lagi jika ada pipe atau koma
                return preg_split('/[|,]/', (string) $p);
            })
            ->filter()
            ->map(fn ($r) => str($r)->lower()->replace(['_', '-'], ' ')->squish()->toString())
            ->map(fn ($r) => ([
                'general manager' => 'gm',
                'generalmanager'  => 'gm',
                'mgr'             => 'manager',
                'super admin'     => 'super_admin',
                'superadmin'      => 'super_admin',
                'sa'              => 'super_admin',
                'root'            => 'super_admin',
            ][$r] ?? $r))
            ->unique()
            ->values();

        // --- Super Admin override ---
        if ($userRole === 'super_admin') {
            return $next($request);
        }

        // --- Cek membership ---
        if (!$allowed->contains($userRole)) {
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
