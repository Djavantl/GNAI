<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se está logado E se o campo 'is_admin' no banco é verdadeiro
        if (auth()->check() && auth()->user()->is_admin) {
            return $next($request);
        }

        // Se não for admin, redireciona ou retorna erro
        abort(403, 'Acesso restrito aos administradores.');
    }
}
