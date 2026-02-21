<?php

namespace App\Http\Controllers;
use Throwable;
use Illuminate\Support\Facades\Log;

abstract class Controller
{
    protected function handleException(Throwable $e, string $fallbackMessage)
    {
        Log::error($e);

        $message = $e->getMessage() ?: $fallbackMessage;

        return redirect()
            ->back()
            ->withInput()
            ->with('error', $message);
    }
}
