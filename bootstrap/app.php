<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // ★ AQUÍ ESTÁ LA MAGIA PARA LARAVEL 11/12 ★
        // Si el usuario ya está logueado e intenta ir al login, mándalo al Panel Admin.
        $middleware->redirectUsersTo(function (Request $request) {
            return route('admin.dashboard');
        });

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();