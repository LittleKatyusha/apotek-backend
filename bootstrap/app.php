<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        channels: __DIR__.'/../routes/channels.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // Daftarkan HandleCors sebagai middleware global.
        // Ini akan berlaku untuk SEMUA rute, termasuk /broadcasting/auth.
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // Tetap daftarkan alias untuk penjaga admin.
        $middleware->alias([
            'auth.admin' => \App\Http\Middleware\IsAdmin::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        $exceptions->render(function (Illuminate\Auth\AuthenticationException $e, Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.'
                ], 401);
            }
        });

    })->create();
