<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InclusiveRadar\{
    AssistiveTechnologyController,
    AccessibleEducationalMaterialController,
    AccessibilityFeatureController,
    BarrierCategoryController,
    BarrierController,
    InstitutionController,
    LoanController,
    LocationController,
    ResourceStatusController,
    ResourceTypeController,
    TypeAttributeAssignmentController,
    TypeAttributeController,
};


//Resource Types & Attributes

Route::get('/resource-types/{resourceType}/attributes', [TypeAttributeAssignmentController::class, 'getAttributesByType'])->name('resource-types.attributes');
Route::get('/type-attribute-assignments', [TypeAttributeAssignmentController::class, 'index'])->name('type-attribute-assignments.index');
Route::get('/type-attribute-assignments/create', [TypeAttributeAssignmentController::class, 'create'])->name('type-attribute-assignments.create');
Route::post('/type-attribute-assignments/store', [TypeAttributeAssignmentController::class, 'store'])->name('type-attribute-assignments.store');
Route::get('/type-attribute-assignments/{assignment}', [TypeAttributeAssignmentController::class, 'show'])->name('type-attribute-assignments.show');
Route::get('/type-attribute-assignments/{assignment}/edit', [TypeAttributeAssignmentController::class, 'edit'])->name('type-attribute-assignments.edit');
Route::put('/type-attribute-assignments/{assignment}', [TypeAttributeAssignmentController::class, 'update'])->name('type-attribute-assignments.update');
Route::delete('/type-attribute-assignments/{assignment}', [TypeAttributeAssignmentController::class, 'destroy'])->name('type-attribute-assignments.destroy');

Route::get('/resource-types', [ResourceTypeController::class, 'index'])->name('resource-types.index');
Route::get('/resource-types/create', [ResourceTypeController::class, 'create'])->name('resource-types.create');
Route::post('/resource-types/store', [ResourceTypeController::class, 'store'])->name('resource-types.store');
Route::get('/resource-types/{resourceType}', [ResourceTypeController::class, 'show'])->name('resource-types.show');
Route::get('/resource-types/{resourceType}/edit', [ResourceTypeController::class, 'edit'])->name('resource-types.edit');
Route::put('/resource-types/{resourceType}', [ResourceTypeController::class, 'update'])->name('resource-types.update');
Route::patch('/resource-types/{resourceType}/toggle', [ResourceTypeController::class, 'toggleActive'])->name('resource-types.toggle');
Route::delete('/resource-types/{resourceType}', [ResourceTypeController::class, 'destroy'])->name('resource-types.destroy');

Route::get('/type-attributes', [TypeAttributeController::class, 'index'])->name('type-attributes.index');
Route::get('/type-attributes/create', [TypeAttributeController::class, 'create'])->name('type-attributes.create');
Route::post('/type-attributes/store', [TypeAttributeController::class, 'store'])->name('type-attributes.store');
Route::get('/type-attributes/{typeAttribute}', [TypeAttributeController::class, 'show'])->name('type-attributes.show');
Route::get('/type-attributes/{typeAttribute}/edit', [TypeAttributeController::class, 'edit'])->name('type-attributes.edit');
Route::put('/type-attributes/{typeAttribute}', [TypeAttributeController::class, 'update'])->name('type-attributes.update');
Route::patch('/type-attributes/{typeAttribute}/toggle', [TypeAttributeController::class, 'toggleActive'])->name('type-attributes.toggle');
Route::delete('/type-attributes/{typeAttribute}', [TypeAttributeController::class, 'destroy'])->name('type-attributes.destroy');

// Assistive Technologies
Route::get('/assistive-technologies', [AssistiveTechnologyController::class, 'index'])->name('assistive-technologies.index');
Route::get('/assistive-technologies/create', [AssistiveTechnologyController::class, 'create'])->name('assistive-technologies.create');
Route::post('/assistive-technologies/store', [AssistiveTechnologyController::class, 'store'])->name('assistive-technologies.store');
Route::get('/assistive-technologies/{assistiveTechnology}', [AssistiveTechnologyController::class, 'show'])->name('assistive-technologies.show'); // <-- aqui
Route::get('/assistive-technologies/{assistiveTechnology}/edit', [AssistiveTechnologyController::class, 'edit'])->name('assistive-technologies.edit');
Route::put('/assistive-technologies/{assistiveTechnology}', [AssistiveTechnologyController::class, 'update'])->name('assistive-technologies.update');
Route::patch('/assistive-technologies/{assistiveTechnology}/toggle', [AssistiveTechnologyController::class, 'toggleActive'])->name('assistive-technologies.toggle');
Route::delete('/assistive-technologies/{assistiveTechnology}', [AssistiveTechnologyController::class, 'destroy'])->name('assistive-technologies.destroy');
Route::get('/assistive-technologies/{assistiveTechnology}/pdf', [AssistiveTechnologyController::class, 'generatePdf'])->name('assistive-technologies.pdf');

// Accessible Educational Materials
Route::get('/accessible-educational-materials', [AccessibleEducationalMaterialController::class, 'index'])->name('accessible-educational-materials.index');
Route::get('/accessible-educational-materials/create', [AccessibleEducationalMaterialController::class, 'create'])->name('accessible-educational-materials.create');
Route::post('/accessible-educational-materials/store', [AccessibleEducationalMaterialController::class, 'store'])->name('accessible-educational-materials.store');
Route::get('/accessible-educational-materials/{material}', [AccessibleEducationalMaterialController::class, 'show'])->name('accessible-educational-materials.show');
Route::get('/accessible-educational-materials/{material}/edit', [AccessibleEducationalMaterialController::class, 'edit'])->name('accessible-educational-materials.edit');
Route::put('/accessible-educational-materials/{material}', [AccessibleEducationalMaterialController::class, 'update'])->name('accessible-educational-materials.update');
Route::patch('/accessible-educational-materials/{material}/toggle', [AccessibleEducationalMaterialController::class, 'toggleActive'])->name('accessible-educational-materials.toggle');
Route::delete('/accessible-educational-materials/{material}', [AccessibleEducationalMaterialController::class, 'destroy'])->name('accessible-educational-materials.destroy');

//Barriers & Infrastructure

Route::get('/barriers', [BarrierController::class, 'index'])->name('barriers.index');
Route::get('/barriers/create', [BarrierController::class, 'create'])->name('barriers.create');
Route::post('/barriers/store', [BarrierController::class, 'store'])->name('barriers.store');
Route::get('/barriers/{barrier}/edit', [BarrierController::class, 'edit'])->name('barriers.edit');
Route::put('/barriers/{barrier}', [BarrierController::class, 'update'])->name('barriers.update');
Route::patch('/barriers/{barrier}/toggle', [BarrierController::class, 'toggleActive'])->name('barriers.toggle');
Route::delete('/barriers/{barrier}', [BarrierController::class, 'destroy'])->name('barriers.destroy');

Route::get('/barrier-categories', [BarrierCategoryController::class, 'index'])->name('barrier-categories.index');
Route::get('/barrier-categories/create', [BarrierCategoryController::class, 'create'])->name('barrier-categories.create');
Route::post('/barrier-categories/store', [BarrierCategoryController::class, 'store'])->name('barrier-categories.store');
Route::get('/barrier-categories/{barrierCategory}', [BarrierCategoryController::class, 'show'])->name('barrier-categories.show');
Route::get('/barrier-categories/{barrierCategory}/edit', [BarrierCategoryController::class, 'edit'])->name('barrier-categories.edit');
Route::put('/barrier-categories/{barrierCategory}', [BarrierCategoryController::class, 'update'])->name('barrier-categories.update');
Route::patch('/barrier-categories/{barrierCategory}/toggle', [BarrierCategoryController::class, 'toggleActive'])->name('barrier-categories.toggle-active');
Route::delete('/barrier-categories/{barrierCategory}', [BarrierCategoryController::class, 'destroy'])->name('barrier-categories.destroy');

//Map Bases & Locations

Route::get('/institutions', [InstitutionController::class, 'index'])->name('institutions.index');
Route::get('/institutions/create', [InstitutionController::class, 'create'])->name('institutions.create');
Route::post('/institutions/store', [InstitutionController::class, 'store'])->name('institutions.store');
Route::get('/institutions/{institution}', [InstitutionController::class, 'show'])->name('institutions.show');
Route::get('/institutions/{institution}/edit', [InstitutionController::class, 'edit'])->name('institutions.edit');
Route::put('/institutions/{institution}', [InstitutionController::class, 'update'])->name('institutions.update');
Route::patch('/institutions/{institution}/toggle', [InstitutionController::class, 'toggleActive'])->name('institutions.toggle-active');
Route::delete('/institutions/{institution}', [InstitutionController::class, 'destroy'])->name('institutions.destroy');

Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
Route::post('/locations/store', [LocationController::class, 'store'])->name('locations.store');
Route::get('/locations/{location}', [LocationController::class, 'show'])->name('locations.show');
Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit');
Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
Route::patch('/locations/{location}/toggle', [LocationController::class, 'toggleActive'])->name('locations.toggle-active');
Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

//Support Services

Route::get('/accessibility-features', [AccessibilityFeatureController::class, 'index'])->name('accessibility-features.index');
Route::get('/accessibility-features/create', [AccessibilityFeatureController::class, 'create'])->name('accessibility-features.create');
Route::post('/accessibility-features/store', [AccessibilityFeatureController::class, 'store'])->name('accessibility-features.store');
Route::get('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'show'])->name('accessibility-features.show');
Route::get('/accessibility-features/{accessibilityFeature}/edit', [AccessibilityFeatureController::class, 'edit'])->name('accessibility-features.edit');
Route::put('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'update'])->name('accessibility-features.update');
Route::patch('/accessibility-features/{accessibilityFeature}/toggle', [AccessibilityFeatureController::class, 'toggleActive'])->name('accessibility-features.toggle');
Route::delete('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'destroy'])->name('accessibility-features.destroy');

Route::get('/resource-statuses', [ResourceStatusController::class, 'index'])->name('resource-statuses.index');
Route::get('/resource-statuses/{resourceStatus}', [ResourceStatusController::class, 'show'])->name('resource-statuses.show');
Route::get('/resource-statuses/{resourceStatus}/edit', [ResourceStatusController::class, 'edit'])->name('resource-statuses.edit');
Route::put('/resource-statuses/{resourceStatus}', [ResourceStatusController::class, 'update'])->name('resource-statuses.update');
Route::patch('/resource-statuses/{resourceStatus}/toggle', [ResourceStatusController::class, 'toggleActive'])->name('resource-statuses.toggle-active');

Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
Route::post('/loans/store', [LoanController::class, 'store'])->name('loans.store');
Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
Route::get('/loans/{loan}/edit', [LoanController::class, 'edit'])->name('loans.edit');
Route::put('/loans/{loan}', [LoanController::class, 'update'])->name('loans.update');
Route::patch('/loans/{loan}/return', [LoanController::class, 'returnItem'])->name('loans.return');
Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');
