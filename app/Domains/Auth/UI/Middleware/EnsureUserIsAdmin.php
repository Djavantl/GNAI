<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Middleware;

use App\Domains\Auth\Domain\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserIsAdmin
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User && $user->isAdmin()) {
            return $next($request);
        }

        abort(403, 'Acesso restrito aos administradores.');
    }
}
