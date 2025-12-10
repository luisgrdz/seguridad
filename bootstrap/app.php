<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Fix para Proxies (Render/Heroku/etc)
        $middleware->trustProxies(at: '*');

        // REGISTRO DE ALIAS
        $middleware->alias([
            // Alias del sistema (aseguramos que auth apunte al oficial)
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,

            // Tus alias personalizados
            'role' => \App\Http\Middleware\CheckRole::class,
            'no_cache' => \App\Http\Middleware\NoCache::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return redirect()->route('login')
                ->with('message', 'Tu sesión ha expirado. Por favor, inicia sesión de nuevo.');
        });
    })
    ->create();
