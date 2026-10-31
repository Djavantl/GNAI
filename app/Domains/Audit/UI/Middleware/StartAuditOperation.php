<?php

declare(strict_types=1);

namespace App\Domains\Audit\UI\Middleware;

use App\Domains\Audit\Infrastructure\AuditContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cada requisição é uma operação de auditoria: agrupa o que ela gravar e guarda os dados do log técnico.
 * Só o caminho da URL: a query string pode carregar tokens.
 */
final class StartAuditOperation
{
    public function __construct(private readonly AuditContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->context->start([
            'origin' => 'web',
            'route' => $request->route()?->getName(),
            'method' => $request->method(),
            'url' => '/'.ltrim($request->path(), '/'),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
        ]);

        return $next($request);
    }
}
