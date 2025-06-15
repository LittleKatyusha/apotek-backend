<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // Mendaftarkan alias 'auth.admin' ke class IsAdmin.
        // Ini adalah langkah yang memperbaiki error "Target class does not exist".
        $middleware->alias([
            'auth.admin' => \App\Http\Middleware\IsAdmin::class,
        ]);

        // Memastikan middleware CORS berjalan untuk semua rute API.
        $middleware->api(prepend: [
             \Illuminate\Http\Middleware\HandleCors::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        // Menangani error jika pengguna belum terotentikasi saat mengakses API.
        $exceptions->render(function (Illuminate\Auth\AuthenticationException $e, Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.'
                ], 401);
            }
        });

    })->create();
