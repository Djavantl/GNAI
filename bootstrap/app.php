<?php

use App\Domains\Auth\UI\Middleware\EnsureUserIsAdmin;
use App\Shared\Application\Exceptions\AccessDeniedException;
use App\Shared\Domain\Exceptions\BusinessRuleException;
use App\Shared\Infrastructure\Http\Middleware\SanitizeRichTextInput;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_PREFIX
        );

        $middleware->web(append: [
            SanitizeRichTextInput::class,
        ]);

        $middleware->redirectTo(
            guests: 'auth/login',
            users: 'auth/dashboard'
        );
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Tratamento para Regras de Negócio
        $exceptions->render(function (BusinessRuleException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        });

        // Acesso negado / autorização
        $exceptions->render(function (AccessDeniedException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 403);
            }

            return response()->view('errors.403', [], 403);
        });

        // Validação
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Dados inválidos.', 'errors' => $e->errors()], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        });

        // Não Autenticado
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Não autenticado.'], 401);
            }

            return redirect()->guest(route('login'))->with('error', 'Faça login para continuar.');
        });

        // Model Not Found (404 de Banco)
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            $msg = 'Recurso não encontrado.';

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 404)
                : redirect()->back()->with('error', $msg);
        });

        // Erro de Banco (Query)
        $exceptions->render(function (QueryException $e, Request $request) {
            logger()->error('Erro de Banco: '.$e->getMessage());
            $msg = 'Erro interno no servidor.';

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : redirect()->back()->with('error', $msg);
        });

    })->create();
