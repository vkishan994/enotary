<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // $middleware->alias([
        //     
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // custom redirect for admin guard
        $exceptions->render(function (AuthenticationException $e, $request) {
            // If route starts with admin or the guard is admin
            if ($request->is('admin/*')) {
                return redirect()->route('adminLogin');
            }

            return redirect()->route('login');
        });
    })->create();
