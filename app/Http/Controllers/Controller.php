<?php

namespace App\Http\Controllers;
use Throwable;
use Illuminate\Support\Facades\Log;
use App\Models\SpecializedEducationalSupport\Semester;

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
