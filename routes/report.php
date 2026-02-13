<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Report\ReportController;

Route::middleware(['auth'])->group(function () {

    // Listar relatórios disponíveis
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index')
        ->middleware('can:report.index');

    // Configurar filtros do relatório
    Route::get('/reports/configure', [ReportController::class, 'configure'])
        ->name('reports.configure')
        ->middleware('can:report.configure');

    // Exportações (PDF, Excel, HTML)
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
        ->name('reports.exportPdf')
        ->middleware('can:report.export');

    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])
        ->name('reports.exportExcel')
        ->middleware('can:report.export');

    Route::get('/reports/export/html', [ReportController::class, 'exportHtml'])
        ->name('reports.export.html')
        ->middleware('can:report.export');
});
