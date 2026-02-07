<?php

use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoans;
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
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            CannotDeleteWithActiveLoans $e,
                                        $request
        ) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->withErrors([
                'delete' => $e->getMessage()
            ]);
        });

    })->create();
