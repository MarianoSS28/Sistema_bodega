<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias nombrados (se usan como string en las rutas)
        $middleware->alias([
            'menu.acceso' => \App\Http\Middleware\CheckMenuAcceso::class,
        ]);

        // Middleware que corre en TODAS las rutas web automáticamente
        $middleware->web(append: [
            \App\Http\Middleware\CheckMantenimiento::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();