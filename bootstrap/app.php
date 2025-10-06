<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // kalau kamu punya api routes, aktifkan baris di bawah:
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /**
         * Alias middleware kustom (opsional tapi praktis).
         * Sekarang kamu bisa pakai di route: ->middleware('role:gm,super_admin')
         */
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        // Contoh menambah middleware ke grup web (opsional)
        // $middleware->appendToGroup('web', \App\Http\Middleware\YourExtraWebMiddleware::class);

        // Contoh global middleware (hati2, ini jalan di semua request)
        // $middleware->append(\App\Http\Middleware\ForceJsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 403 dari Gate / policy
        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This action is unauthorized.'], 403);
            }
            return response()->view('errors.403', ['message' => $e->getMessage()], 403);
        });

        // 404 model tidak ditemukan
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Resource not found'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // Validasi (return JSON rapi jika request JSON)
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });
    })
    ->create();
