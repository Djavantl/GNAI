<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait ResolvesBackRoute
{
    protected function resolveBackRoute(Request $request, string $defaultRoute): string
    {
        return $request->query('back')
            ?? route($defaultRoute);
    }
}
