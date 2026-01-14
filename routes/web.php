<?php

use Illuminate\Support\Facades\Route;

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
});
