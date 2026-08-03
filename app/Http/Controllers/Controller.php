<?php

namespace App\Http\Controllers;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class Controller
{
    protected function semesters()
    {
        return Semester::orderByDesc('year')
            ->orderByDesc('term')
            ->get(['id', 'label']);
    }

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
