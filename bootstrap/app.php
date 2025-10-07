<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        // api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /**
         * ====== Middleware Aliases ======
         * Pakai di routes:
         *   ->middleware('role:gm|super_admin')  // atau 'role:gm,super_admin'
         *   ->middleware('verified')
         */
        $middleware->alias([
            'role'     => \App\Http\Middleware\EnsureUserHasRole::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            // 'can' bawaan Laravel; tak perlu alias
        ]);

        // Contoh menambahkan middleware ke group (opsional):
        // $middleware->appendToGroup('web', \App\Http\Middleware\SomeWebMiddleware::class);

        // Hati-hati menambahkan global middleware:
        // $middleware->append(\App\Http\Middleware\ForceJsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // 401 Unauthenticated
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // Redirect ke login; fallback ke /login jika route name 'login' tidak tersedia
            try {
                $loginUrl = route('login', absolute: false);
            } catch (\Throwable $t) {
                $loginUrl = '/login';
            }
            return redirect()->guest($loginUrl);
        });

        // 403 Unauthorized (Gate/Policy)
        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This action is unauthorized.'], 403);
            }
            return response()->view('errors.403', [
                'message' => $e->getMessage() ?: 'You do not have permission to access this resource.',
            ], 403);
        });

        // 404 Model tidak ditemukan (Eloquent)
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Resource not found'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // 404 Not Found (route tidak ada)
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Endpoint not found'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // 405 Method Not Allowed
        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Method not allowed'], 405);
            }
            return response()->view('errors.405', [], 405);
        });

        // 419 CSRF token mismatch
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Page expired. Please refresh and try again.'], 419);
            }
            return response()->view('errors.419', [], 419);
        });

        // 422 Validasi (JSON rapi)
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors'  => $e->errors(),
                ], 422);
            }
            // Untuk request web biasa, Laravel akan redirect back dengan errors otomatis.
        });

        // 429 Rate limit
        $exceptions->render(function (TooManyRequestsHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Too many requests'], 429);
            }
            return response()->view('errors.429', [], 429);
        });

        // Fallback 500 (opsional)
        // $exceptions->respond(function ($response) {
        //     if ($response->getStatusCode() === 500) {
        //         return response()->view('errors.500', [], 500);
        //     }
        //     return $response;
        // });
    })
    ->create();
