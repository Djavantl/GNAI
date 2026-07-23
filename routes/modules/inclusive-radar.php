<?php

use App\Domains\InclusiveRadar\UI\Controllers\BarrierCategoryController as DomainBarrierCategoryController;
use App\Domains\InclusiveRadar\UI\Controllers\AccessibleEducationalMaterialController as DomainAccessibleEducationalMaterialController;
use App\Domains\InclusiveRadar\UI\Controllers\AssistiveTechnologyController as DomainAssistiveTechnologyController;
use App\Domains\InclusiveRadar\UI\Controllers\InstitutionalEventController as DomainInstitutionalEventController;
use App\Domains\InclusiveRadar\UI\Controllers\LoanController as DomainLoanController;
use App\Domains\InclusiveRadar\UI\Controllers\LocationController as DomainLocationController;
use App\Domains\InclusiveRadar\UI\Controllers\WaitlistController as DomainWaitlistController;
use App\Domains\InclusiveRadar\UI\Controllers\AccessibilityFeatureController;
use App\Http\Controllers\InclusiveRadar\BarrierController;
use App\Domains\InclusiveRadar\UI\Controllers\InstitutionController;
use App\Http\Controllers\InclusiveRadar\Logs\AccessibleEducationalMaterialLogController;
use App\Http\Controllers\InclusiveRadar\Logs\AssistiveTechnologyLogController;
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
    Route::get('/barrier-categories', [DomainBarrierCategoryController::class, 'index'])
        ->name('barrier-categories.index')->middleware('can:barrier-category.index');
    Route::get('/barrier-categories/create', [DomainBarrierCategoryController::class, 'create'])
        ->name('barrier-categories.create')->middleware('can:barrier-category.create');
    Route::post('/barrier-categories/store', [DomainBarrierCategoryController::class, 'store'])
        ->name('barrier-categories.store')->middleware('can:barrier-category.store');
    Route::get('/barrier-categories/{barrierCategory}', [DomainBarrierCategoryController::class, 'show'])
        ->name('barrier-categories.show')->middleware('can:barrier-category.show');
    Route::get('/barrier-categories/{barrierCategory}/edit', [DomainBarrierCategoryController::class, 'edit'])
        ->name('barrier-categories.edit')->middleware('can:barrier-category.edit');
    Route::put('/barrier-categories/{barrierCategory}', [DomainBarrierCategoryController::class, 'update'])
        ->name('barrier-categories.update')->middleware('can:barrier-category.update');
    Route::delete('/barrier-categories/{barrierCategory}', [DomainBarrierCategoryController::class, 'destroy'])
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
    Route::get('/locations', [DomainLocationController::class, 'index'])
        ->name('locations.index')->middleware('can:location.index');
    Route::get('/locations/create', [DomainLocationController::class, 'create'])
        ->name('locations.create')->middleware('can:location.create');
    Route::post('/locations/store', [DomainLocationController::class, 'store'])
        ->name('locations.store')->middleware('can:location.store');
    Route::get('/locations/{location}', [DomainLocationController::class, 'show'])
        ->name('locations.show')->middleware('can:location.show');
    Route::get('/locations/{location}/edit', [DomainLocationController::class, 'edit'])
        ->name('locations.edit')->middleware('can:location.edit');
    Route::put('/locations/{location}', [DomainLocationController::class, 'update'])
        ->name('locations.update')->middleware('can:location.update');
    Route::delete('/locations/{location}', [DomainLocationController::class, 'destroy'])
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

    // ------------------- TECNOLOGIAS ASSISTIVAS -------------------
    Route::get('/assistive-technologies', [DomainAssistiveTechnologyController::class, 'index'])
        ->name('assistive-technologies.index')->middleware('can:assistive-technology.index');

    Route::get('/assistive-technologies/create', [DomainAssistiveTechnologyController::class, 'create'])
        ->name('assistive-technologies.create')->middleware('can:assistive-technology.create');

    Route::post('/assistive-technologies/store', [DomainAssistiveTechnologyController::class, 'store'])
        ->name('assistive-technologies.store')->middleware('can:assistive-technology.store');

    Route::get('assistive-technologies/{assistiveTechnology}/inspection/{inspection}', [DomainAssistiveTechnologyController::class, 'showInspection'])
        ->name('assistive-technologies.inspection.show')->middleware('can:assistive-technology.inspection.show');

    Route::get('/assistive-technologies/{assistiveTechnology}', [DomainAssistiveTechnologyController::class, 'show'])
        ->name('assistive-technologies.show')->middleware('can:assistive-technology.show');

    Route::get('/assistive-technologies/{assistiveTechnology}/edit', [DomainAssistiveTechnologyController::class, 'edit'])
        ->name('assistive-technologies.edit')->middleware('can:assistive-technology.edit');

    Route::put('/assistive-technologies/{assistiveTechnology}', [DomainAssistiveTechnologyController::class, 'update'])
        ->name('assistive-technologies.update')->middleware('can:assistive-technology.update');

    Route::delete('/assistive-technologies/{assistiveTechnology}', [DomainAssistiveTechnologyController::class, 'destroy'])
        ->name('assistive-technologies.destroy')->middleware('can:assistive-technology.destroy');

    Route::get('/assistive-technologies/{assistiveTechnology}/pdf', [DomainAssistiveTechnologyController::class, 'generatePdf'])
        ->name('assistive-technologies.pdf')->middleware('can:assistive-technology.pdf');

    Route::get('/assistive-technologies/{assistiveTechnology}/logs', [AssistiveTechnologyLogController::class, 'index'])
        ->name('assistive-technologies.logs')->middleware('can:assistive-technology.logs');

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

    // ------------------- MATERIAIS PEDAGÓGICOS ACESSÍVEIS -------------------
    Route::get('/accessible-educational-materials', [DomainAccessibleEducationalMaterialController::class, 'index'])
        ->name('accessible-educational-materials.index')->middleware('can:material.index');

    Route::get('/accessible-educational-materials/create', [DomainAccessibleEducationalMaterialController::class, 'create'])
        ->name('accessible-educational-materials.create')->middleware('can:material.create');

    Route::post('/accessible-educational-materials/store', [DomainAccessibleEducationalMaterialController::class, 'store'])
        ->name('accessible-educational-materials.store')->middleware('can:material.store');

    Route::get('accessible-educational-materials/{material}/inspection/{inspection}', [DomainAccessibleEducationalMaterialController::class, 'showInspection'])
        ->name('accessible-educational-materials.inspection.show')->middleware('can:material.inspection.show');

    Route::get('/accessible-educational-materials/{material}', [DomainAccessibleEducationalMaterialController::class, 'show'])
        ->name('accessible-educational-materials.show')->middleware('can:material.show');

    Route::get('/accessible-educational-materials/{material}/edit', [DomainAccessibleEducationalMaterialController::class, 'edit'])
        ->name('accessible-educational-materials.edit')->middleware('can:material.edit');

    Route::put('/accessible-educational-materials/{material}', [DomainAccessibleEducationalMaterialController::class, 'update'])
        ->name('accessible-educational-materials.update')->middleware('can:material.update');

    Route::delete('/accessible-educational-materials/{material}', [DomainAccessibleEducationalMaterialController::class, 'destroy'])
        ->name('accessible-educational-materials.destroy')->middleware('can:material.destroy');

    Route::get('/accessible-educational-materials/{material}/pdf', [DomainAccessibleEducationalMaterialController::class, 'generatePdf'])
        ->name('accessible-educational-materials.pdf')->middleware('can:material.pdf');

    Route::get('/accessible-educational-materials/{material}/logs', [AccessibleEducationalMaterialLogController::class, 'index'])
        ->name('accessible-educational-materials.logs')->middleware('can:material.logs');

    // ------------------- AGENDA INSTITUCIONAL -------------------
    Route::get('/institutional-events', [DomainInstitutionalEventController::class, 'index'])
        ->name('institutional-events.index')->middleware('can:institutional-event.index');

    Route::get('/institutional-events/create', [DomainInstitutionalEventController::class, 'create'])
        ->name('institutional-events.create')->middleware('can:institutional-event.create');

    Route::post('/institutional-events/store', [DomainInstitutionalEventController::class, 'store'])
        ->name('institutional-events.store')->middleware('can:institutional-event.store');

    Route::get('/institutional-events/{event}', [DomainInstitutionalEventController::class, 'show'])
        ->name('institutional-events.show')->middleware('can:institutional-event.show');

    Route::get('/institutional-events/{event}/edit', [DomainInstitutionalEventController::class, 'edit'])
        ->name('institutional-events.edit')->middleware('can:institutional-event.edit');

    Route::put('/institutional-events/{event}', [DomainInstitutionalEventController::class, 'update'])
        ->name('institutional-events.update')->middleware('can:institutional-event.update');

    Route::delete('/institutional-events/{event}', [DomainInstitutionalEventController::class, 'destroy'])
        ->name('institutional-events.destroy')->middleware('can:institutional-event.destroy');

    Route::get('/institutional-events/{event}/pdf', [DomainInstitutionalEventController::class, 'generatePdf'])
        ->name('institutional-events.pdf')->middleware('can:institutional-event.pdf');

    // ------------------- EMPRÉSTIMOS -------------------
    Route::get('/loans', [DomainLoanController::class, 'index'])
        ->name('loans.index')->middleware('can:loan.index');

    Route::get('/loans/create', [DomainLoanController::class, 'create'])
        ->name('loans.create')->middleware('can:loan.create');

    Route::post('/loans/store', [DomainLoanController::class, 'store'])
        ->name('loans.store')->middleware('can:loan.store');

    Route::get('/loans/{loan}', [DomainLoanController::class, 'show'])
        ->name('loans.show')->middleware('can:loan.show');

    Route::get('/loans/{loan}/edit', [DomainLoanController::class, 'edit'])
        ->name('loans.edit')->middleware('can:loan.edit');

    Route::put('/loans/{loan}', [DomainLoanController::class, 'update'])
        ->name('loans.update')->middleware('can:loan.update');

    Route::patch('/loans/{loan}/return', [DomainLoanController::class, 'returnItem'])
        ->name('loans.return')->middleware('can:loan.return');

    Route::delete('/loans/{loan}', [DomainLoanController::class, 'destroy'])
        ->name('loans.destroy')->middleware('can:loan.destroy');

    Route::get('/loans/{loan}/pdf', [DomainLoanController::class, 'generatePdf'])
        ->name('loans.pdf')->middleware('can:loan.pdf');

    // ------------------- FILA DE ESPERA -------------------
    Route::get('/waitlists', [DomainWaitlistController::class, 'index'])
        ->name('waitlists.index')->middleware('can:waitlist.index');

    Route::get('/waitlists/create', [DomainWaitlistController::class, 'create'])
        ->name('waitlists.create')->middleware('can:waitlist.create');

    Route::post('/waitlists/store', [DomainWaitlistController::class, 'store'])
        ->name('waitlists.store')->middleware('can:waitlist.store');

    Route::get('/waitlists/{waitlist}', [DomainWaitlistController::class, 'show'])
        ->name('waitlists.show')->middleware('can:waitlist.show');

    Route::get('/waitlists/{waitlist}/edit', [DomainWaitlistController::class, 'edit'])
        ->name('waitlists.edit')->middleware('can:waitlist.edit');

    Route::put('/waitlists/{waitlist}', [DomainWaitlistController::class, 'update'])
        ->name('waitlists.update')->middleware('can:waitlist.update');

    Route::delete('/waitlists/{waitlist}', [DomainWaitlistController::class, 'destroy'])
        ->name('waitlists.destroy')->middleware('can:waitlist.destroy');

    Route::patch('/waitlists/{waitlist}/cancel', [DomainWaitlistController::class, 'cancel'])
        ->name('waitlists.cancel')->middleware('can:waitlist.cancel');

    Route::get('/waitlists/{waitlist}/pdf', [DomainWaitlistController::class, 'generatePdf'])
        ->name('waitlists.pdf')->middleware('can:waitlist.pdf');
});
