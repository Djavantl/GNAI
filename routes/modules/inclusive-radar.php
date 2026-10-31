<?php

use App\Domains\InclusiveRadar\UI\Controllers\AccessibilityFeatureController;
use App\Domains\InclusiveRadar\UI\Controllers\BarrierCategoryController;
use App\Domains\InclusiveRadar\UI\Controllers\BarrierController;
use App\Domains\InclusiveRadar\UI\Controllers\InstitutionalEventController;
use App\Domains\InclusiveRadar\UI\Controllers\InstitutionController;
use App\Domains\InclusiveRadar\UI\Controllers\ItemController;
use App\Domains\InclusiveRadar\UI\Controllers\LoanController;
use App\Domains\InclusiveRadar\UI\Controllers\LocationController;
use App\Domains\InclusiveRadar\UI\Controllers\WaitlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ADMIN – Gestão de Cadastros (somente administradores)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {});

/*
|--------------------------------------------------------------------------
| OPERACIONAL – Recursos e Ações do Dia a Dia (autenticado + permissões)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // ------------------- CATEGORIAS DAS BARREIRAS -------------------
    Route::get('/barrier-categories', [BarrierCategoryController::class, 'index'])
        ->name('barrier-categories.index')->middleware('can:barrier-category.index');
    Route::get('/barrier-categories/create', [BarrierCategoryController::class, 'create'])
        ->name('barrier-categories.create')->middleware('can:barrier-category.create');
    Route::post('/barrier-categories/store', [BarrierCategoryController::class, 'store'])
        ->name('barrier-categories.store')->middleware('can:barrier-category.store');
    Route::get('/barrier-categories/{barrierCategory}', [BarrierCategoryController::class, 'show'])
        ->name('barrier-categories.show')->middleware('can:barrier-category.show');
    Route::get('/barrier-categories/{barrierCategory}/edit', [BarrierCategoryController::class, 'edit'])
        ->name('barrier-categories.edit')->middleware('can:barrier-category.edit');
    Route::put('/barrier-categories/{barrierCategory}', [BarrierCategoryController::class, 'update'])
        ->name('barrier-categories.update')->middleware('can:barrier-category.update');
    Route::delete('/barrier-categories/{barrierCategory}', [BarrierCategoryController::class, 'destroy'])
        ->name('barrier-categories.destroy')->middleware('can:barrier-category.destroy');

    // ------------------- INSTITUIÇÕES -------------------
    Route::get('/institutions', [InstitutionController::class, 'index'])
        ->name('institutions.index')->middleware('can:institution.index');
    Route::get('/institutions/create', [InstitutionController::class, 'create'])
        ->name('institutions.create')->middleware('can:institution.create');
    Route::post('/institutions/store', [InstitutionController::class, 'store'])
        ->name('institutions.store')->middleware('can:institution.store');
    Route::get('/institutions/{institution}', [InstitutionController::class, 'show'])
        ->name('institutions.show')->middleware('can:institution.show');
    Route::get('/institutions/{institution}/edit', [InstitutionController::class, 'edit'])
        ->name('institutions.edit')->middleware('can:institution.edit');
    Route::put('/institutions/{institution}', [InstitutionController::class, 'update'])
        ->name('institutions.update')->middleware('can:institution.update');
    Route::delete('/institutions/{institution}', [InstitutionController::class, 'destroy'])
        ->name('institutions.destroy')->middleware('can:institution.destroy');

    // ------------------- LOCALIZAÇÕES -------------------
    Route::get('/locations', [LocationController::class, 'index'])
        ->name('locations.index')->middleware('can:location.index');
    Route::get('/locations/create', [LocationController::class, 'create'])
        ->name('locations.create')->middleware('can:location.create');
    Route::post('/locations/store', [LocationController::class, 'store'])
        ->name('locations.store')->middleware('can:location.store');
    Route::get('/locations/{location}', [LocationController::class, 'show'])
        ->name('locations.show')->middleware('can:location.show');
    Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])
        ->name('locations.edit')->middleware('can:location.edit');
    Route::put('/locations/{location}', [LocationController::class, 'update'])
        ->name('locations.update')->middleware('can:location.update');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])
        ->name('locations.destroy')->middleware('can:location.destroy');

    // ------------------- RECURSOS DE ACESSIBILIDADE -------------------
    Route::get('/accessibility-features', [AccessibilityFeatureController::class, 'index'])
        ->name('accessibility-features.index')->middleware('can:accessibility-feature.index');
    Route::get('/accessibility-features/create', [AccessibilityFeatureController::class, 'create'])
        ->name('accessibility-features.create')->middleware('can:accessibility-feature.create');
    Route::post('/accessibility-features/store', [AccessibilityFeatureController::class, 'store'])
        ->name('accessibility-features.store')->middleware('can:accessibility-feature.store');
    Route::get('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'show'])
        ->name('accessibility-features.show')->middleware('can:accessibility-feature.show');
    Route::get('/accessibility-features/{accessibilityFeature}/edit', [AccessibilityFeatureController::class, 'edit'])
        ->name('accessibility-features.edit')->middleware('can:accessibility-feature.edit');
    Route::put('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'update'])
        ->name('accessibility-features.update')->middleware('can:accessibility-feature.update');
    Route::delete('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'destroy'])
        ->name('accessibility-features.destroy')->middleware('can:accessibility-feature.destroy');

    // ------------------- ITENS (TECNOLOGIAS ASSISTIVAS E MATERIAIS PEDAGÓGICOS) -------------------
    Route::get('/items', [ItemController::class, 'index'])
        ->name('items.index')->middleware('can:item.index');

    Route::get('/items/create', [ItemController::class, 'create'])
        ->name('items.create')->middleware('can:item.create');

    Route::post('/items/store', [ItemController::class, 'store'])
        ->name('items.store')->middleware('can:item.store');

    Route::get('/items/{item}/clone', [ItemController::class, 'clone'])
        ->name('items.clone')->middleware('can:item.create');

    Route::get('/items/{item}/inspection/{inspection}', [ItemController::class, 'showInspection'])
        ->name('items.inspection.show')->middleware('can:item.inspection.show');

    Route::get('/items/{item}', [ItemController::class, 'show'])
        ->name('items.show')->middleware('can:item.show');

    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])
        ->name('items.edit')->middleware('can:item.edit');

    Route::put('/items/{item}', [ItemController::class, 'update'])
        ->name('items.update')->middleware('can:item.update');

    Route::delete('/items/{item}', [ItemController::class, 'destroy'])
        ->name('items.destroy')->middleware('can:item.destroy');

    Route::get('/items/{item}/pdf', [ItemController::class, 'generatePdf'])
        ->name('items.pdf')->middleware('can:item.pdf');

    // ------------------- BARREIRAS -------------------
    Route::get('/barriers', [BarrierController::class, 'index'])
        ->name('barriers.index')->middleware('can:barrier.index');

    Route::get('/barriers/create', [BarrierController::class, 'create'])
        ->name('barriers.create')->middleware('can:barrier.create');

    Route::post('/barriers/store', [BarrierController::class, 'store'])
        ->name('barriers.store')->middleware('can:barrier.store');

    Route::get('barriers/{barrier}/inspection/{inspection}', [BarrierController::class, 'showInspection'])
        ->name('barriers.inspection.show')->middleware('can:barrier.inspection.show');

    Route::get('/barriers/{barrier}', [BarrierController::class, 'show'])
        ->name('barriers.show')->middleware('can:barrier.show');

    Route::get('/barriers/{barrier}/edit', [BarrierController::class, 'edit'])
        ->name('barriers.edit')->middleware('can:barrier.edit');

    Route::put('/barriers/{barrier}', [BarrierController::class, 'update'])
        ->name('barriers.update')->middleware('can:barrier.update');

    Route::delete('/barriers/{barrier}', [BarrierController::class, 'destroy'])
        ->name('barriers.destroy')->middleware('can:barrier.destroy');

    Route::get('/barriers/{barrier}/pdf', [BarrierController::class, 'generatePdf'])
        ->name('barriers.pdf')->middleware('can:barrier.pdf');

    // ------------------- AGENDA INSTITUCIONAL -------------------
    Route::get('/institutional-events', [InstitutionalEventController::class, 'index'])
        ->name('institutional-events.index')->middleware('can:institutional-event.index');

    Route::get('/institutional-events/create', [InstitutionalEventController::class, 'create'])
        ->name('institutional-events.create')->middleware('can:institutional-event.create');

    Route::post('/institutional-events/store', [InstitutionalEventController::class, 'store'])
        ->name('institutional-events.store')->middleware('can:institutional-event.store');

    Route::get('/institutional-events/{event}', [InstitutionalEventController::class, 'show'])
        ->name('institutional-events.show')->middleware('can:institutional-event.show');

    Route::get('/institutional-events/{event}/edit', [InstitutionalEventController::class, 'edit'])
        ->name('institutional-events.edit')->middleware('can:institutional-event.edit');

    Route::put('/institutional-events/{event}', [InstitutionalEventController::class, 'update'])
        ->name('institutional-events.update')->middleware('can:institutional-event.update');

    Route::delete('/institutional-events/{event}', [InstitutionalEventController::class, 'destroy'])
        ->name('institutional-events.destroy')->middleware('can:institutional-event.destroy');

    Route::get('/institutional-events/{event}/pdf', [InstitutionalEventController::class, 'generatePdf'])
        ->name('institutional-events.pdf')->middleware('can:institutional-event.pdf');

    // ------------------- EMPRÉSTIMOS -------------------
    Route::get('/loans', [LoanController::class, 'index'])
        ->name('loans.index')->middleware('can:loan.index');

    Route::get('/loans/create', [LoanController::class, 'create'])
        ->name('loans.create')->middleware('can:loan.create');

    Route::post('/loans/store', [LoanController::class, 'store'])
        ->name('loans.store')->middleware('can:loan.store');

    Route::get('/loans/{loan}', [LoanController::class, 'show'])
        ->name('loans.show')->middleware('can:loan.show');

    Route::get('/loans/{loan}/edit', [LoanController::class, 'edit'])
        ->name('loans.edit')->middleware('can:loan.edit');

    Route::put('/loans/{loan}', [LoanController::class, 'update'])
        ->name('loans.update')->middleware('can:loan.update');

    Route::patch('/loans/{loan}/return', [LoanController::class, 'returnItem'])
        ->name('loans.return')->middleware('can:loan.return');

    Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])
        ->name('loans.destroy')->middleware('can:loan.destroy');

    Route::get('/loans/{loan}/pdf', [LoanController::class, 'generatePdf'])
        ->name('loans.pdf')->middleware('can:loan.pdf');

    // ------------------- FILA DE ESPERA -------------------
    Route::get('/waitlists', [WaitlistController::class, 'index'])
        ->name('waitlists.index')->middleware('can:waitlist.index');

    Route::get('/waitlists/create', [WaitlistController::class, 'create'])
        ->name('waitlists.create')->middleware('can:waitlist.create');

    Route::post('/waitlists/store', [WaitlistController::class, 'store'])
        ->name('waitlists.store')->middleware('can:waitlist.store');

    Route::get('/waitlists/{waitlist}', [WaitlistController::class, 'show'])
        ->name('waitlists.show')->middleware('can:waitlist.show');

    Route::get('/waitlists/{waitlist}/edit', [WaitlistController::class, 'edit'])
        ->name('waitlists.edit')->middleware('can:waitlist.edit');

    Route::put('/waitlists/{waitlist}', [WaitlistController::class, 'update'])
        ->name('waitlists.update')->middleware('can:waitlist.update');

    Route::delete('/waitlists/{waitlist}', [WaitlistController::class, 'destroy'])
        ->name('waitlists.destroy')->middleware('can:waitlist.destroy');

    Route::patch('/waitlists/{waitlist}/cancel', [WaitlistController::class, 'cancel'])
        ->name('waitlists.cancel')->middleware('can:waitlist.cancel');

    Route::get('/waitlists/{waitlist}/pdf', [WaitlistController::class, 'generatePdf'])
        ->name('waitlists.pdf')->middleware('can:waitlist.pdf');
});
