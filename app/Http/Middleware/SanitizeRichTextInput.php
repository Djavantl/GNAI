<?php

namespace App\Http\Middleware;

use App\Support\RichTextSanitizer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeRichTextInput
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch')) {
            $request->merge(RichTextSanitizer::sanitizeMixed($request->all()));
        }

        return $next($request);
    }
}
