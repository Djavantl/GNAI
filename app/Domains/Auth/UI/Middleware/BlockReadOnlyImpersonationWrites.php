<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Middleware;

use App\Domains\Auth\Infrastructure\Session\ImpersonationSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Na personificação somente leitura, só consultas passam; encerrar a
 * personificação e sair continuam permitidos.
 */
final class BlockReadOnlyImpersonationWrites
{
    private const ALLOWED_ROUTES = ['admin.impersonate.leave', 'logout'];

    private const MESSAGE = 'Você está no modo somente leitura. Volte para a sua conta e entre novamente com acesso completo para fazer alterações.';

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->isMethodSafe()
            || ! $request->hasSession()
            || ! app(ImpersonationSession::class)->readOnly()
            || $request->routeIs(...self::ALLOWED_ROUTES)
        ) {
            return $next($request);
        }

        if ($request->expectsJson() && ! $request->header('X-Inertia')) {
            return response()->json(['message' => self::MESSAGE], 403);
        }

        return redirect()->back()->with('warning', self::MESSAGE);
    }
}
