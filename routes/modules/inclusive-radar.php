<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InclusiveRadar\{AssistiveTechnologyController,
    AccessibleEducationalMaterialController,
    AccessibilityFeatureController,
    BarrierCategoryController,
    BarrierController,
    InstitutionalEventController,
    InstitutionController,
    LoanController,
    LocationController,
    Logs\AccessibleEducationalMaterialLogController,
    Logs\AssistiveTechnologyLogController,
    TrainingController,
    WaitlistController};

/*
|--------------------------------------------------------------------------
| ADMIN – Gestão de Cadastros (somente administradores)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // ------------------- BARREIRAS E CATEGORIAS -------------------
    Route::get('/barrier-categories', [BarrierCategoryController::class, 'index'])
        ->name('barrier-categories.index');
    Route::get('/barrier-categories/create', [BarrierCategoryController::class, 'create'])
        ->name('barrier-categories.create');
    Route::post('/barrier-categories/store', [BarrierCategoryController::class, 'store'])
        ->name('barrier-categories.store');
    Route::get('/barrier-categories/{barrierCategory}', [BarrierCategoryController::class, 'show'])
        ->name('barrier-categories.show');
    Route::get('/barrier-categories/{barrierCategory}/edit', [BarrierCategoryController::class, 'edit'])
        ->name('barrier-categories.edit');
    Route::put('/barrier-categories/{barrierCategory}', [BarrierCategoryController::class, 'update'])
        ->name('barrier-categories.update');
    Route::delete('/barrier-categories/{barrierCategory}', [BarrierCategoryController::class, 'destroy'])
        ->name('barrier-categories.destroy');

    // ------------------- INSTITUIÇÕES E LOCALIZAÇÕES -------------------
    Route::get('/institutions', [InstitutionController::class, 'index'])
        ->name('institutions.index');
    Route::get('/institutions/create', [InstitutionController::class, 'create'])
        ->name('institutions.create');
    Route::post('/institutions/store', [InstitutionController::class, 'store'])
        ->name('institutions.store');
    Route::get('/institutions/{institution}', [InstitutionController::class, 'show'])
        ->name('institutions.show');
    Route::get('/institutions/{institution}/edit', [InstitutionController::class, 'edit'])
        ->name('institutions.edit');
    Route::put('/institutions/{institution}', [InstitutionController::class, 'update'])
        ->name('institutions.update');
    Route::delete('/institutions/{institution}', [InstitutionController::class, 'destroy'])
        ->name('institutions.destroy');

    Route::get('/locations', [LocationController::class, 'index'])
        ->name('locations.index');
    Route::get('/locations/create', [LocationController::class, 'create'])
        ->name('locations.create');
    Route::post('/locations/store', [LocationController::class, 'store'])
        ->name('locations.store');
    Route::get('/locations/{location}', [LocationController::class, 'show'])
        ->name('locations.show');
    Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])
        ->name('locations.edit');
    Route::put('/locations/{location}', [LocationController::class, 'update'])
        ->name('locations.update');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])
        ->name('locations.destroy');

    // ------------------- RECURSOS DE ACESSIBILIDADE -------------------
    Route::get('/accessibility-features', [AccessibilityFeatureController::class, 'index'])
        ->name('accessibility-features.index');
    Route::get('/accessibility-features/create', [AccessibilityFeatureController::class, 'create'])
        ->name('accessibility-features.create');
    Route::post('/accessibility-features/store', [AccessibilityFeatureController::class, 'store'])
        ->name('accessibility-features.store');
    Route::get('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'show'])
        ->name('accessibility-features.show');
    Route::get('/accessibility-features/{accessibilityFeature}/edit', [AccessibilityFeatureController::class, 'edit'])
        ->name('accessibility-features.edit');
    Route::put('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'update'])
        ->name('accessibility-features.update');
    Route::delete('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'destroy'])
        ->name('accessibility-features.destroy');
});

/*
|--------------------------------------------------------------------------
| OPERACIONAL – Recursos e Ações do Dia a Dia (autenticado + permissões)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // ------------------- TECNOLOGIAS ASSISTIVAS -------------------
    Route::get('/assistive-technologies', [AssistiveTechnologyController::class, 'index'])
        ->name('assistive-technologies.index')->middleware('can:assistive-technology.index');

    Route::get('/assistive-technologies/create', [AssistiveTechnologyController::class, 'create'])
        ->name('assistive-technologies.create')->middleware('can:assistive-technology.create');

    Route::post('/assistive-technologies/store', [AssistiveTechnologyController::class, 'store'])
        ->name('assistive-technologies.store')->middleware('can:assistive-technology.store');

    Route::get('assistive-technologies/{assistiveTechnology}/inspection/{inspection}', [AssistiveTechnologyController::class, 'showInspection'])
        ->name('assistive-technologies.inspection.show')->middleware('can:assistive-technology.inspection.show');;

    Route::get('/assistive-technologies/{assistiveTechnology}', [AssistiveTechnologyController::class, 'show'])
        ->name('assistive-technologies.show')->middleware('can:assistive-technology.show');

    Route::get('/assistive-technologies/{assistiveTechnology}/edit', [AssistiveTechnologyController::class, 'edit'])
        ->name('assistive-technologies.edit')->middleware('can:assistive-technology.edit');

    Route::put('/assistive-technologies/{assistiveTechnology}', [AssistiveTechnologyController::class, 'update'])
        ->name('assistive-technologies.update')->middleware('can:assistive-technology.update');

    Route::delete('/assistive-technologies/{assistiveTechnology}', [AssistiveTechnologyController::class, 'destroy'])
        ->name('assistive-technologies.destroy')->middleware('can:assistive-technology.destroy');

    Route::get('/assistive-technologies/{assistiveTechnology}/pdf', [AssistiveTechnologyController::class, 'generatePdf'])
        ->name('assistive-technologies.pdf')->middleware('can:assistive-technology.pdf');

    Route::get('/assistive-technologies/{assistiveTechnology}/logs', [AssistiveTechnologyLogController::class, 'index'])
        ->name('assistive-technologies.logs')
        ->middleware('can:assistive-technology.logs');

    Route::get('/assistive-technologies/{assistiveTechnology}/logs/pdf', [AssistiveTechnologyLogController::class, 'generatePdf']
    )->name('assistive-technologies.logs.pdf')
    ->middleware('can:assistive-technology.logs.pdf');

    // ------------------- BARREIRAS (RADAR INCLUSIVO) -------------------

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

    // ------------------- MATERIAIS PEDAGÓGICOS ACESSÍVEIS -------------------
    Route::get('/accessible-educational-materials', [AccessibleEducationalMaterialController::class, 'index'])
        ->name('accessible-educational-materials.index')->middleware('can:material.index');

    Route::get('/accessible-educational-materials/create', [AccessibleEducationalMaterialController::class, 'create'])
        ->name('accessible-educational-materials.create')->middleware('can:material.create');

    Route::post('/accessible-educational-materials/store', [AccessibleEducationalMaterialController::class, 'store'])
        ->name('accessible-educational-materials.store')->middleware('can:material.store');

    Route::get('accessible-educational-materials/{material}/inspection/{inspection}', [AccessibleEducationalMaterialController::class, 'showInspection'])
        ->name('accessible-educational-materials.inspection.show')->middleware('can:material.inspection.show');

    Route::get('/accessible-educational-materials/{material}', [AccessibleEducationalMaterialController::class, 'show'])
        ->name('accessible-educational-materials.show')->middleware('can:material.show');

    Route::get('/accessible-educational-materials/{material}/edit', [AccessibleEducationalMaterialController::class, 'edit'])
        ->name('accessible-educational-materials.edit')->middleware('can:material.edit');

    Route::put('/accessible-educational-materials/{material}', [AccessibleEducationalMaterialController::class, 'update'])
        ->name('accessible-educational-materials.update')->middleware('can:material.update');

    Route::delete('/accessible-educational-materials/{material}', [AccessibleEducationalMaterialController::class, 'destroy'])
        ->name('accessible-educational-materials.destroy')->middleware('can:material.destroy');

    Route::get('/accessible-educational-materials/{material}/pdf', [AccessibleEducationalMaterialController::class, 'generatePdf'])
        ->name('accessible-educational-materials.pdf')->middleware('can:material.pdf');

    Route::get('/accessible-educational-materials/{material}/logs', [AccessibleEducationalMaterialLogController::class, 'index'])
        ->name('accessible-educational-materials.logs')->middleware('can:material.logs');

    Route::get('/accessible-educational-materials/{material}/logs/pdf', [AccessibleEducationalMaterialLogController::class, 'generatePdf'])
        ->name('accessible-educational-materials.logs.pdf')->middleware('can:material.logs.pdf');



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
