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
        $middleware->use([
            'App\Http\Middleware\HttpsProtocol',
        ]);
        
        $middleware->alias([
            'cors' => \App\Http\Middleware\CORS::class,
            'jwt.auth' => \App\Http\Middleware\VerifyJWTToken::class,
            'isAdmin' => \App\Http\Middleware\AdminMiddleware::class,
            'attendance.kiosk_or_jwt' => \App\Http\Middleware\AttendanceKioskOrJwt::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
