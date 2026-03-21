<?php

use App\Exceptions\InclusiveRadar\CannotChangeStatusWithActiveLoansException;
use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Exceptions\InclusiveRadar\CannotDeleteLinkedBarrierException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: 'auth/login',
            users: 'auth/dashboard'
        );
        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Empréstimos ativos
        $exceptions->render(function (
            CannotDeleteWithActiveLoansException $e,
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

        $exceptions->render(function (CannotDeleteWithActiveLoansException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['delete' => $e->getMessage()]);
        });

        $exceptions->render(function (CannotDeleteLinkedBarrierException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['delete' => $e->getMessage()]);
        });

        $exceptions->render(function (
            DomainException|InvalidArgumentException $e,
            Request $request
        ) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'business_rule' => $e->getMessage()
                ]);
        });

    })->create();
