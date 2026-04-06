<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;

Route::middleware('web')->group(function () {

    Route::prefix('inclusive-radar')
        ->name('inclusive-radar.')
        ->group(base_path('routes/modules/inclusive-radar.php'));

    Route::prefix('specialized-educational-support')
        ->name('specialized-educational-support.')
        ->group(base_path('routes/modules/specialized-educational-support.php'));

    Route::prefix('backup')
        ->name('backup.')
        ->group(base_path('routes/modules/backup.php'));

    Route::middleware(['auth'])->prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'builder'])->name('reports.index');
        Route::get('/builder', [ReportController::class, 'builder'])->name('reports.builder');
        Route::get('/builder/available', [ReportController::class, 'availableEntities'])->name('reports.available');
        Route::get('/builder/meta', [ReportController::class, 'meta'])->name('reports.meta');
        Route::post('/builder/run', [ReportController::class, 'run'])->name('reports.run');
        Route::post('/builder/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    });

    Route::prefix('auth')
        ->name('')
        ->group(base_path('routes/auth.php'));

    Route::middleware(['auth'])->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
        Route::get('/notifications/list', [NotificationController::class, 'list'])->name('notifications.list');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');
    });
});
