<?php

use App\Domains\Reporting\UI\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function (): void {
    Route::get('/', [ReportController::class, 'index'])
        ->name('index')
        ->middleware('can:report.index');
    Route::get('/builder', [ReportController::class, 'index'])
        ->name('builder')
        ->middleware('can:report.builder');
    Route::get('/sources', [ReportController::class, 'sources'])
        ->name('sources')
        ->middleware('can:report.available');
    Route::get('/metadata', [ReportController::class, 'metadata'])
        ->name('metadata')
        ->middleware('can:report.meta');
    Route::post('/run', [ReportController::class, 'run'])
        ->name('run')
        ->middleware('can:report.run');
    Route::post('/export/pdf', [ReportController::class, 'exportPdf'])
        ->name('export.pdf')
        ->middleware('can:report.pdf');
});
