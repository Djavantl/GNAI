<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Report\ReportController;

// Reports (Relatórios)
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
