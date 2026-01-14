<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InclusiveRadar\{
    AssistiveTechnologyController,
    AssistiveTechnologyStatusController,
    AssistiveTechnologyImageController,
    AccessibleEducationalMaterialController,
    AccessibleEducationalMaterialStatusController,
    AccessibleEducationalMaterialImageController,
    AccessibilityFeatureController,
    BarrierStatusController
};

//Assistive Technology Statuses

Route::get('/assistive-technologies-statuses', [AssistiveTechnologyStatusController::class, 'index'])->name('assistive-technologies-statuses.index');
Route::get('/assistive-technologies-statuses/create', [AssistiveTechnologyStatusController::class, 'create'])->name('assistive-technologies-statuses.create');
Route::post('/assistive-technologies-statuses/store', [AssistiveTechnologyStatusController::class, 'store'])->name('assistive-technologies-statuses.store');
Route::get('/assistive-technologies-statuses/{assistiveTechnologyStatus}/edit', [AssistiveTechnologyStatusController::class, 'edit'])->name('assistive-technologies-statuses.edit');
Route::put('/assistive-technologies-statuses/{assistiveTechnologyStatus}', [AssistiveTechnologyStatusController::class, 'update'])->name('assistive-technologies-statuses.update');
Route::patch('/assistive-technologies-statuses/{assistiveTechnologyStatus}/toggle', [AssistiveTechnologyStatusController::class, 'toggleActive'])->name('assistive-technologies-statuses.toggle');
Route::delete('/assistive-technologies-statuses/{assistiveTechnologyStatus}', [AssistiveTechnologyStatusController::class, 'destroy'])->name('assistive-technologies-statuses.destroy');

//Assistive Technologies

Route::get('/assistive-technologies', [AssistiveTechnologyController::class, 'index'])->name('assistive-technologies.index');
Route::get('/assistive-technologies/create', [AssistiveTechnologyController::class, 'create'])->name('assistive-technologies.create');
Route::post('/assistive-technologies/store', [AssistiveTechnologyController::class, 'store'])->name('assistive-technologies.store');
Route::get('/assistive-technologies/{assistiveTechnology}/edit', [AssistiveTechnologyController::class, 'edit'])->name('assistive-technologies.edit');
Route::put('/assistive-technologies/{assistiveTechnology}', [AssistiveTechnologyController::class, 'update'])->name('assistive-technologies.update');
Route::patch('/assistive-technologies/{assistiveTechnology}/toggle', [AssistiveTechnologyController::class, 'toggleActive'])->name('assistive-technologies.toggle');
Route::delete('/assistive-technologies/{assistiveTechnology}', [AssistiveTechnologyController::class, 'destroy'])->name('assistive-technologies.destroy');

//Assistive Technology Images

Route::post('/assistive-technologies/{assistiveTechnology}/images/store', [AssistiveTechnologyImageController::class, 'store'])->name('assistive-technologies.images.store');
Route::delete('/assistive-technologies/images/{image}', [AssistiveTechnologyImageController::class, 'destroy'])->name('assistive-technologies.images.destroy');

//Accessible Educational Material Statuses

Route::get('/accessible-educational-material-statuses', [AccessibleEducationalMaterialStatusController::class, 'index'])->name('accessible-educational-material-statuses.index');
Route::get('/accessible-educational-material-statuses/create', [AccessibleEducationalMaterialStatusController::class, 'create'])->name('accessible-educational-material-statuses.create');
Route::post('/accessible-educational-material-statuses/store', [AccessibleEducationalMaterialStatusController::class, 'store'])->name('accessible-educational-material-statuses.store');
Route::get('/accessible-educational-material-statuses/{status}/edit', [AccessibleEducationalMaterialStatusController::class, 'edit'])->name('accessible-educational-material-statuses.edit');
Route::put('/accessible-educational-material-statuses/{status}', [AccessibleEducationalMaterialStatusController::class, 'update'])->name('accessible-educational-material-statuses.update');
Route::patch('/accessible-educational-material-statuses/{status}/toggle', [AccessibleEducationalMaterialStatusController::class, 'toggleActive'])->name('accessible-educational-material-statuses.toggle');
Route::delete('/accessible-educational-material-statuses/{status}', [AccessibleEducationalMaterialStatusController::class, 'destroy'])->name('accessible-educational-material-statuses.destroy');

//Accessible Educational Materials

Route::get('/accessible-educational-materials', [AccessibleEducationalMaterialController::class, 'index'])->name('accessible-educational-materials.index');
Route::get('/accessible-educational-materials/create', [AccessibleEducationalMaterialController::class, 'create'])->name('accessible-educational-materials.create');
Route::post('/accessible-educational-materials/store', [AccessibleEducationalMaterialController::class, 'store'])->name('accessible-educational-materials.store');
Route::get('/accessible-educational-materials/{accessibleEducationalMaterial}/edit', [AccessibleEducationalMaterialController::class, 'edit'])->name('accessible-educational-materials.edit');
Route::put('/accessible-educational-materials/{accessibleEducationalMaterial}', [AccessibleEducationalMaterialController::class, 'update'])->name('accessible-educational-materials.update');
Route::patch('/accessible-educational-materials/{accessibleEducationalMaterial}/toggle', [AccessibleEducationalMaterialController::class, 'toggleActive'])->name('accessible-educational-materials.toggle');
Route::delete('/accessible-educational-materials/{accessibleEducationalMaterial}', [AccessibleEducationalMaterialController::class, 'destroy'])->name('accessible-educational-materials.destroy');

//Accessible Educational Material Images

Route::post('/accessible-educational-materials/{material}/images/store', [AccessibleEducationalMaterialImageController::class, 'store'])->name('accessible-educational-materials.images.store');
Route::delete('/accessible-educational-materials/images/{image}', [AccessibleEducationalMaterialImageController::class, 'destroy'])->name('accessible-educational-materials.images.destroy');

//Accessibility Features

Route::get('/accessibility-features', [AccessibilityFeatureController::class, 'index'])->name('accessibility-features.index');
Route::get('/accessibility-features/create', [AccessibilityFeatureController::class, 'create'])->name('accessibility-features.create');
Route::post('/accessibility-features/store', [AccessibilityFeatureController::class, 'store'])->name('accessibility-features.store');
Route::get('/accessibility-features/{accessibilityFeature}/edit', [AccessibilityFeatureController::class, 'edit'])->name('accessibility-features.edit');
Route::put('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'update'])->name('accessibility-features.update');
Route::patch('/accessibility-features/{accessibilityFeature}/toggle', [AccessibilityFeatureController::class, 'toggleActive'])->name('accessibility-features.toggle');
Route::delete('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'destroy'])->name('accessibility-features.destroy');

//Barrier Statuses

Route::get('/barrier-statuses', [BarrierStatusController::class, 'index'])->name('barrier-statuses.index');
Route::get('/barrier-statuses/create', [BarrierStatusController::class, 'create'])->name('barrier-statuses.create');
Route::post('/barrier-statuses/store', [BarrierStatusController::class, 'store'])->name('barrier-statuses.store');
Route::get('/barrier-statuses/{barrierStatus}/edit', [BarrierStatusController::class, 'edit'])->name('barrier-statuses.edit');
Route::put('/barrier-statuses/{barrierStatus}', [BarrierStatusController::class, 'update'])->name('barrier-statuses.update');
Route::patch('/barrier-statuses/{barrierStatus}/toggle', [BarrierStatusController::class, 'toggleActive'])->name('barrier-statuses.toggle');
Route::delete('/barrier-statuses/{barrierStatus}', [BarrierStatusController::class, 'destroy'])->name('barrier-statuses.destroy');
