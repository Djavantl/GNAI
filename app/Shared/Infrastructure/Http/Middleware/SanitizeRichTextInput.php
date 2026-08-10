<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Middleware;

use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SanitizeRichTextInput
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch')) {
            $request->merge(RichTextSanitizer::sanitizeMixed($request->all()));
        }

        return $next($request);
    }
}
