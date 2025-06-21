<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// The 'use Illuminate\Http\Middleware\HandleCors;' line can stay, it won't hurt,
// but it's not strictly necessary here once you remove the problematic 'replace' block.
// Keeping it is fine for clarity if HandleCors is used elsewhere in this file.

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            // Keep this line. It ensures HandleCors is part of your API middleware stack.
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // ====================================================================
        // !!! REMOVE THE FOLLOWING BLOCK COMPLETELY !!!
        // This is the part causing the TypeError.
        // $middleware->replace(HandleCors::class, function ($request) {
        //     return (new HandleCors)->handle(
        //         $request,
        //         function () use ($request) { return $request; },
        //         ['http://localhost:3000'],
        //         ['*'],
        //         ['*'],
        //         null,
        //         null,
        //         true
        //     );
        // });
        // ====================================================================


        // You can uncomment and modify your web middleware if needed
        // $middleware->web(append: [
        //     \App\Http\Middleware\HandleInertiaRequests::class,
        //     \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();