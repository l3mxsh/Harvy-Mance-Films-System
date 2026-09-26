<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.only' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (ThrottleRequestsException $e) {
            $headers = $e->getHeaders();
            $retryAfter = $headers['Retry-After'] ?? 60;
            $maxAttempts = $headers['X-RateLimit-Limit'] ?? 5;

            return back()->withErrors([
                'email' => "Too many login attempts. You've used all {$maxAttempts} attempts. Please try again in {$retryAfter} seconds.",
            ])->onlyInput('email');
        });
    })->create();
