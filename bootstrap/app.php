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
    ->withMiddleware(function (Middleware $middleware): void {
        /*
         |----------------------------------------------------------------------
         | Global middleware (optional)
         |----------------------------------------------------------------------
         | Register any global middleware here if needed.
         */
        // $middleware->global(\App\Http\Middleware\TrustProxies::class);
        // $middleware->global(\Fruitcake\Cors\HandleCors::class);
        // $middleware->global(\App\Http\Middleware\PreventRequestsDuringMaintenance::class);

        /*
         |----------------------------------------------------------------------
         | Route middleware aliases
         |----------------------------------------------------------------------
         | Register route middleware aliases in a single array.
         */
        $middleware->alias([
            'auth'     => \App\Http\Middleware\Authenticate::class,
            'guest'    => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            // add additional aliases here if you need them, for example:
            // 'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // 'signed'   => \Illuminate\Routing\Middleware\ValidateSignature::class,
            // 'csrf'     => \App\Http\Middleware\VerifyCsrfToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
