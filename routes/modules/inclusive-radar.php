<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InclusiveRadar\{AssistiveTechnologyController,
    AccessibleEducationalMaterialController,
    AccessibilityFeatureController,
    BarrierCategoryController,
    BarrierController,
    InstitutionController,
    LoanController,
    LocationController,
    Logs\AccessibleEducationalMaterialLogController,
    Logs\AssistiveTechnologyLogController,
    MaintenanceController,
    ResourceStatusController,
    ResourceTypeController,
    TrainingController,
    TypeAttributeAssignmentController,
    TypeAttributeController,
    WaitlistController};

/*
|--------------------------------------------------------------------------
| ADMIN – Gestão de Cadastros (somente administradores)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // ------------------- TIPOS DE RECURSOS E ATRIBUTOS -------------------
    Route::get('/resource-types', [ResourceTypeController::class, 'index'])
        ->name('resource-types.index');
    Route::get('/resource-types/create', [ResourceTypeController::class, 'create'])
        ->name('resource-types.create');
    Route::post('/resource-types/store', [ResourceTypeController::class, 'store'])
        ->name('resource-types.store');
    Route::get('/resource-types/{resourceType}', [ResourceTypeController::class, 'show'])
        ->name('resource-types.show');
    Route::get('/resource-types/{resourceType}/edit', [ResourceTypeController::class, 'edit'])
        ->name('resource-types.edit');
    Route::put('/resource-types/{resourceType}', [ResourceTypeController::class, 'update'])
        ->name('resource-types.update');
    Route::patch('/resource-types/{resourceType}/toggle', [ResourceTypeController::class, 'toggleActive'])
        ->name('resource-types.toggle');
    Route::delete('/resource-types/{resourceType}', [ResourceTypeController::class, 'destroy'])
        ->name('resource-types.destroy');

    Route::get('/type-attributes', [TypeAttributeController::class, 'index'])
        ->name('type-attributes.index');
    Route::get('/type-attributes/create', [TypeAttributeController::class, 'create'])
        ->name('type-attributes.create');
    Route::post('/type-attributes/store', [TypeAttributeController::class, 'store'])
        ->name('type-attributes.store');
    Route::get('/type-attributes/{typeAttribute}', [TypeAttributeController::class, 'show'])
        ->name('type-attributes.show');
    Route::get('/type-attributes/{typeAttribute}/edit', [TypeAttributeController::class, 'edit'])
        ->name('type-attributes.edit');
    Route::put('/type-attributes/{typeAttribute}', [TypeAttributeController::class, 'update'])
        ->name('type-attributes.update');
    Route::patch('/type-attributes/{typeAttribute}/toggle', [TypeAttributeController::class, 'toggleActive'])
        ->name('type-attributes.toggle');
    Route::delete('/type-attributes/{typeAttribute}', [TypeAttributeController::class, 'destroy'])
        ->name('type-attributes.destroy');

    Route::get('/type-attribute-assignments', [TypeAttributeAssignmentController::class, 'index'])
        ->name('type-attribute-assignments.index');
    Route::get('/type-attribute-assignments/create', [TypeAttributeAssignmentController::class, 'create'])
        ->name('type-attribute-assignments.create');
    Route::post('/type-attribute-assignments/store', [TypeAttributeAssignmentController::class, 'store'])
        ->name('type-attribute-assignments.store');
    Route::get('/type-attribute-assignments/{assignment}', [TypeAttributeAssignmentController::class, 'show'])
        ->name('type-attribute-assignments.show');
    Route::get('/type-attribute-assignments/{assignment}/edit', [TypeAttributeAssignmentController::class, 'edit'])
        ->name('type-attribute-assignments.edit');
    Route::put('/type-attribute-assignments/{assignment}', [TypeAttributeAssignmentController::class, 'update'])
        ->name('type-attribute-assignments.update');
    Route::delete('/type-attribute-assignments/{assignment}', [TypeAttributeAssignmentController::class, 'destroy'])
        ->name('type-attribute-assignments.destroy');

    Route::get('/resource-types/{resourceType}/attributes', [TypeAttributeAssignmentController::class, 'getAttributesByType'])
        ->name('resource-types.attributes');

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
    Route::patch('/barrier-categories/{barrierCategory}/toggle', [BarrierCategoryController::class, 'toggleActive'])
        ->name('barrier-categories.toggle');
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
    Route::patch('/institutions/{institution}/toggle', [InstitutionController::class, 'toggleActive'])
        ->name('institutions.toggle');
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
    Route::patch('/locations/{location}/toggle', [LocationController::class, 'toggleActive'])
        ->name('locations.toggle');
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
    Route::patch('/accessibility-features/{accessibilityFeature}/toggle', [AccessibilityFeatureController::class, 'toggleActive'])
        ->name('accessibility-features.toggle');
    Route::delete('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'destroy'])
        ->name('accessibility-features.destroy');

    // ------------------- STATUS DE RECURSO -------------------
    Route::get('/resource-statuses', [ResourceStatusController::class, 'index'])
        ->name('resource-statuses.index');
    Route::get('/resource-statuses/{resourceStatus}', [ResourceStatusController::class, 'show'])
        ->name('resource-statuses.show');
    Route::get('/resource-statuses/{resourceStatus}/edit', [ResourceStatusController::class, 'edit'])
        ->name('resource-statuses.edit');
    Route::put('/resource-statuses/{resourceStatus}', [ResourceStatusController::class, 'update'])
        ->name('resource-statuses.update');
    Route::patch('/resource-statuses/{resourceStatus}/toggle', [ResourceStatusController::class, 'toggleActive'])
        ->name('resource-statuses.toggle');
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

    Route::patch('/assistive-technologies/{assistiveTechnology}/toggle', [AssistiveTechnologyController::class, 'toggleActive'])
        ->name('assistive-technologies.toggle')->middleware('can:assistive-technology.toggle');

    Route::delete('/assistive-technologies/{assistiveTechnology}', [AssistiveTechnologyController::class, 'destroy'])
        ->name('assistive-technologies.destroy')->middleware('can:assistive-technology.destroy');

    Route::get('/assistive-technologies/{assistiveTechnology}/pdf', [AssistiveTechnologyController::class, 'generatePdf'])
        ->name('assistive-technologies.pdf')->middleware('can:assistive-technology.pdf');

    Route::get('/assistive-technologies/{assistiveTechnology}/excel', [AssistiveTechnologyController::class, 'exportExcel'])
        ->name('assistive-technologies.excel')->middleware('can:assistive-technology.excel');


    Route::get('/assistive-technologies/{assistiveTechnology}/logs', [AssistiveTechnologyLogController::class, 'index'])
        ->name('assistive-technologies.logs')
        ->middleware('can:assistive-technology.logs');

    Route::get('/assistive-technologies/{assistiveTechnology}/logs/pdf', [AssistiveTechnologyLogController::class, 'generatePdf']
    )->name('assistive-technologies.logs.pdf')
    ->middleware('can:assistive-technology.logs.pdf');


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
    Route::patch('/barriers/{barrier}/toggle', [BarrierController::class, 'toggleActive'])
        ->name('barriers.toggle')->middleware('can:barrier.toggle');
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

    Route::patch('/accessible-educational-materials/{material}/toggle', [AccessibleEducationalMaterialController::class, 'toggleActive'])
        ->name('accessible-educational-materials.toggle')->middleware('can:material.toggle');

    Route::delete('/accessible-educational-materials/{material}', [AccessibleEducationalMaterialController::class, 'destroy'])
        ->name('accessible-educational-materials.destroy')->middleware('can:material.destroy');

    Route::get('/accessible-educational-materials/{material}/pdf', [AccessibleEducationalMaterialController::class, 'generatePdf'])
        ->name('accessible-educational-materials.pdf')->middleware('can:material.pdf');

    Route::get('/accessible-educational-materials/{material}/logs', [AccessibleEducationalMaterialLogController::class, 'index'])
        ->name('accessible-educational-materials.logs')->middleware('can:material.logs');

    Route::get('/accessible-educational-materials/{material}/logs/pdf', [AccessibleEducationalMaterialLogController::class, 'generatePdf'])
        ->name('accessible-educational-materials.logs.pdf')->middleware('can:material.logs.pdf');



    // ------------------- TREINAMENTOS -------------------

    Route::get('/trainings', [TrainingController::class, 'index'])
        ->name('trainings.index')->middleware('can:training.index');

    Route::get('/trainings/create', [TrainingController::class, 'create'])
        ->name('trainings.create')->middleware('can:training.create');

    Route::post('/trainings/store', [TrainingController::class, 'store'])
        ->name('trainings.store')->middleware('can:training.store');

    Route::delete('trainings/{training}/files/{file}', [TrainingController::class, 'destroyFile'])
        ->name('trainings.files.destroy');

    Route::get('/trainings/{training}', [TrainingController::class, 'show'])
        ->name('trainings.show')->middleware('can:training.show');

    Route::get('/trainings/{training}/edit', [TrainingController::class, 'edit'])
        ->name('trainings.edit')->middleware('can:training.edit');

    Route::put('/trainings/{training}', [TrainingController::class, 'update'])
        ->name('trainings.update')->middleware('can:training.update');

    Route::patch('/trainings/{training}/toggle', [TrainingController::class, 'toggleActive'])
        ->name('trainings.toggle')->middleware('can:training.toggle');

    Route::delete('/trainings/{training}', [TrainingController::class, 'destroy'])
        ->name('trainings.destroy')->middleware('can:training.destroy');

    Route::get('/trainings/{training}/pdf', [TrainingController::class, 'generatePdf'])
        ->name('trainings.pdf')->middleware('can:training.pdf');

    Route::get('/trainings/{training}/excel', [TrainingController::class, 'exportExcel'])
        ->name('trainings.excel')->middleware('can:training.export');


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

    // Dashboard – lista manutenções
    Route::get('/maintenances', [MaintenanceController::class, 'index'])
        ->name('maintenances.index')
        ->middleware('can:maintenance.index');

    // Show – detalhes de uma manutenção
    Route::get('/maintenances/{maintenance}', [MaintenanceController::class, 'show'])
        ->name('maintenances.show')
        ->middleware('can:maintenance.show');

    // ===================== ETAPAS =====================

    // Step 0 – abrir manutenção
    Route::post('/maintenances/{assistiveTechnology}/open', [MaintenanceController::class, 'openMaintenanceRequest'])
        ->name('maintenances.open')
        ->middleware('can:maintenance.create');

    // Etapa 1 – mostrar formulário
    Route::get('/maintenances/{maintenance}/stage1', [MaintenanceController::class, 'stage1'])
        ->name('maintenances.stage1')
        ->middleware('can:maintenance.stage1');

    // Etapa 1 – iniciar/concluir
    Route::patch('/maintenances/{maintenance}/stage1', [MaintenanceController::class, 'saveStage1'])
        ->name('maintenances.saveStage1')
        ->middleware('can:maintenance.stage1');

    // Etapa 2 – mostrar formulário
    Route::get('/maintenances/{maintenance}/stage2', [MaintenanceController::class, 'stage2'])
        ->name('maintenances.stage2')
        ->middleware('can:maintenance.stage2');

    // Etapa 2 – iniciar/concluir
    Route::patch('/maintenances/{maintenance}/stage2', [MaintenanceController::class, 'saveStage2'])
        ->name('maintenances.saveStage2')
        ->middleware('can:maintenance.stage2');

    // Gerar PDF
    Route::get('/maintenances/{maintenance}/pdf', [MaintenanceController::class, 'generatePdf'])
        ->name('maintenances.pdf')
        ->middleware('can:maintenance.pdf');


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
