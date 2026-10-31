<?php

use App\Domains\Website\UI\Controllers\PublicWebsiteController;
use App\Domains\Website\UI\Controllers\WebsiteBuilderController;
use Illuminate\Support\Facades\Route;

// Site público white label (sem autenticação)

Route::prefix('site')->group(function (): void {
    Route::get('/', [PublicWebsiteController::class, 'home'])->name('home');
    Route::get('/equipe', [PublicWebsiteController::class, 'team'])->name('team');
    Route::get('/publicacoes', [PublicWebsiteController::class, 'publications'])->name('publications');
    Route::get('/publicacoes/{slug}', [PublicWebsiteController::class, 'publication'])
        ->where('slug', '[a-z0-9-]+')
        ->name('publications.show');
});

// Retaguarda do site

Route::middleware(['auth'])->prefix('website')->group(function (): void {
    Route::get('/builder', [WebsiteBuilderController::class, 'edit'])
        ->name('builder.edit')->middleware('can:website.edit');

    Route::put('/builder', [WebsiteBuilderController::class, 'update'])
        ->name('builder.update')->middleware('can:website.update');
});
