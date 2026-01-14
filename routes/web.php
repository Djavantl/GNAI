<?php

use App\Http\Controllers\AccessibleEducationalMaterialImageController;
use App\Http\Controllers\AssistiveTechnologyImageController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BarrierStatusController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AssistiveTechnologyStatusController;
use App\Http\Controllers\DeficiencyController;
use App\Http\Controllers\AssistiveTechnologyController;
use App\Http\Controllers\AccessibilityFeatureController;
use App\Http\Controllers\AccessibleEducationalMaterialController;
use App\Http\Controllers\AccessibleEducationalMaterialStatusController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;

Route::get('/people', [PersonController::class, 'index'])->name('people.index');
Route::get('/people/create', [PersonController::class, 'create'])->name('people.create');
Route::post('/people/store', [PersonController::class, 'store'])->name('people.store');
Route::get('/people/{person}/edit', [PersonController::class, 'edit'])->name('people.edit');
Route::put('/people/{person}', [PersonController::class, 'update'])->name('people.update');
Route::delete('/people/{person}', [PersonController::class, 'destroy'])->name('people.destroy');

Route::get('/assistive-technologies-statuses', [AssistiveTechnologyStatusController::class, 'index'])->name('assistive-technologies-statuses.index');
Route::get('/assistive-technologies-statuses/create', [AssistiveTechnologyStatusController::class, 'create'])->name('assistive-technologies-statuses.create');
Route::post('/assistive-technologies-statuses/store', [AssistiveTechnologyStatusController::class, 'store'])->name('assistive-technologies-statuses.store');
Route::get('/assistive-technologies-statuses/{assistiveTechnologyStatus}/edit', [AssistiveTechnologyStatusController::class, 'edit'])->name('assistive-technologies-statuses.edit');
Route::put('/assistive-technologies-statuses/{assistiveTechnologyStatus}', [AssistiveTechnologyStatusController::class, 'update'])->name('assistive-technologies-statuses.update');
Route::patch('/assistive-technologies-statuses/{assistive_technology_status}/toggle', [AssistiveTechnologyStatusController::class, 'toggleActive'])->name('assistive-technologies-statuses.deactivate');
Route::delete('/assistive-technologies-statuses/{assistive_technology_status}', [AssistiveTechnologyStatusController::class, 'destroy'])->name('assistive-technologies-statuses.destroy');

Route::get('/deficiencies', [DeficiencyController::class, 'index'])->name('deficiencies.index');
Route::get('/deficiencies/create', [DeficiencyController::class, 'create'])->name('deficiencies.create');
Route::post('/deficiencies/store', [DeficiencyController::class, 'store'])->name('deficiencies.store');
Route::get('/deficiencies/{deficiency}/edit', [DeficiencyController::class, 'edit'])->name('deficiencies.edit');
Route::put('/deficiencies/{deficiency}', [DeficiencyController::class, 'update'])->name('deficiencies.update');
Route::patch('/deficiencies/{deficiency}/deactivate', [DeficiencyController::class, 'toggleActive'])->name('deficiencies.deactivate');
Route::delete('/deficiencies/{deficiency}', [DeficiencyController::class, 'destroy'])->name('deficiencies.destroy');

Route::get('/assistive-technologies', [AssistiveTechnologyController::class, 'index'])->name('assistive-technologies.index');
Route::get('/assistive-technologies/create', [AssistiveTechnologyController::class, 'create'])->name('assistive-technologies.create');
Route::post('/assistive-technologies/store', [AssistiveTechnologyController::class, 'store'])->name('assistive-technologies.store');
Route::get('/assistive-technologies/{assistiveTechnology}/edit', [AssistiveTechnologyController::class, 'edit'])->name('assistive-technologies.edit');
Route::put('/assistive-technologies/{assistiveTechnology}', [AssistiveTechnologyController::class, 'update'])->name('assistive-technologies.update');
Route::patch('/assistive-technologies/{assistiveTechnology}/toggle', [AssistiveTechnologyController::class, 'toggleActive'])->name('assistive-technologies.toggle');
Route::delete('/assistive-technologies/{assistiveTechnology}', [AssistiveTechnologyController::class, 'destroy'])->name('assistive-technologies.destroy');

Route::get('/positions', [PositionController::class, 'index'])->name('positions.index');
Route::get('/positions/create', [PositionController::class, 'create'])->name('positions.create');
Route::post('/positions/store', [PositionController::class, 'store'])->name('positions.store');
Route::get('/positions/{position}/edit', [PositionController::class, 'edit'])->name('positions.edit');
Route::put('/positions/{position}', [PositionController::class, 'update'])->name('positions.update');
Route::patch('/positions/{position}/deactivate', [PositionController::class, 'toggleActive'])->name('positions.deactivate');
Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');

Route::get('/accessible-educational-material-statuses', [AccessibleEducationalMaterialStatusController::class, 'index'])->name('accessible-educational-material-statuses.index');
Route::get('/accessible-educational-material-statuses/create', [AccessibleEducationalMaterialStatusController::class, 'create'])->name('accessible-educational-material-statuses.create');
Route::post('/accessible-educational-material-statuses/store', [AccessibleEducationalMaterialStatusController::class, 'store'])->name('accessible-educational-material-statuses.store');
Route::get('/accessible-educational-material-statuses/{status}/edit', [AccessibleEducationalMaterialStatusController::class, 'edit'])->name('accessible-educational-material-statuses.edit');
Route::put('/accessible-educational-material-statuses/{status}', [AccessibleEducationalMaterialStatusController::class, 'update'])->name('accessible-educational-material-statuses.update');
Route::patch('/accessible-educational-material-statuses/{status}/toggle', [AccessibleEducationalMaterialStatusController::class, 'toggleActive'])->name('accessible-educational-material-statuses.deactivate');
Route::delete('/accessible-educational-material-statuses/{status}', [AccessibleEducationalMaterialStatusController::class, 'destroy'])->name('accessible-educational-material-statuses.destroy');

Route::get('/accessible-educational-materials', [AccessibleEducationalMaterialController::class, 'index'])->name('accessible-educational-materials.index');
Route::get('/accessible-educational-materials/create', [AccessibleEducationalMaterialController::class, 'create'])->name('accessible-educational-materials.create');
Route::post('/accessible-educational-materials/store', [AccessibleEducationalMaterialController::class, 'store'])->name('accessible-educational-materials.store');
Route::get('/accessible-educational-materials/{accessibleEducationalMaterial}/edit', [AccessibleEducationalMaterialController::class, 'edit'])->name('accessible-educational-materials.edit');
Route::put('/accessible-educational-materials/{accessibleEducationalMaterial}', [AccessibleEducationalMaterialController::class, 'update'])->name('accessible-educational-materials.update');
Route::patch('/accessible-educational-materials/{accessibleEducationalMaterial}/toggle', [AccessibleEducationalMaterialController::class, 'toggleActive'])->name('accessible-educational-materials.toggle');
Route::delete('/accessible-educational-materials/{accessibleEducationalMaterial}', [AccessibleEducationalMaterialController::class, 'destroy'])->name('accessible-educational-materials.destroy');

Route::get('/accessibility-features', [AccessibilityFeatureController::class, 'index'])->name('accessibility-features.index');
Route::get('/accessibility-features/create', [AccessibilityFeatureController::class, 'create'])->name('accessibility-features.create');
Route::post('/accessibility-features/store', [AccessibilityFeatureController::class, 'store'])->name('accessibility-features.store');
Route::get('/accessibility-features/{accessibilityFeature}/edit', [AccessibilityFeatureController::class, 'edit'])->name('accessibility-features.edit');
Route::put('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'update'])->name('accessibility-features.update');
Route::patch('/accessibility-features/{accessibilityFeature}/toggle', [AccessibilityFeatureController::class, 'toggleActive'])->name('accessibility-features.toggle');
Route::delete('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'destroy'])->name('accessibility-features.destroy');

Route::post('/assistive-technologies/{assistiveTechnology}/images/store', [AssistiveTechnologyImageController::class, 'store'])->name('assistive-technologies.images.store');
Route::delete('/assistive-technologies/images/{image}', [AssistiveTechnologyImageController::class, 'destroy'])->name('assistive-technologies.images.destroy');

Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
Route::post('/backups/store', [BackupController::class, 'store'])->name('backups.store');
Route::get('/backups/{id}/edit', [BackupController::class, 'edit'])->name('backups.edit');
Route::put('/backups/{id}', [BackupController::class, 'update'])->name('backups.update');
Route::get('/backups/{id}/download', [BackupController::class, 'download'])->name('backups.download');
Route::delete('/backups/{id}', [BackupController::class, 'destroy'])->name('backups.destroy');

Route::post('/accessible-educational-materials/{material}/images/store', [AccessibleEducationalMaterialImageController::class, 'store'])->name('accessible-educational-materials.images.store');
Route::delete('/accessible-educational-materials/images/{image}', [AccessibleEducationalMaterialImageController::class, 'destroy'])->name('accessible-educational-materials.images.destroy');

Route::get('/barrier-statuses', [BarrierStatusController::class, 'index'])->name('barrier-statuses.index');
Route::get('/barrier-statuses/create', [BarrierStatusController::class, 'create'])->name('barrier-statuses.create');
Route::post('/barrier-statuses/store', [BarrierStatusController::class, 'store'])->name('barrier-statuses.store');
Route::get('/barrier-statuses/{barrierStatus}/edit', [BarrierStatusController::class, 'edit'])->name('barrier-statuses.edit');
Route::put('/barrier-statuses/{barrierStatus}', [BarrierStatusController::class, 'update'])->name('barrier-statuses.update');
Route::patch('/barrier-statuses/{barrierStatus}/toggle', [BarrierStatusController::class, 'toggleActive'])->name('barrier-statuses.toggle');
Route::delete('/barrier-statuses/{barrierStatus}', [BarrierStatusController::class, 'destroy'])->name('barrier-statuses.destroy');

Route::get('/semesters', [SemesterController::class, 'index'])->name('semesters.index');
Route::get('/semesters/create', [SemesterController::class, 'create'])->name('semesters.create');
Route::post('/semesters/store', [SemesterController::class, 'store'])->name('semesters.store');
Route::get('/semesters/{semester}/edit', [SemesterController::class, 'edit'])->name('semesters.edit');
Route::put('/semesters/{semester}', [SemesterController::class, 'update'])->name('semesters.update');
Route::patch('/semesters/{semester}/set-current', [SemesterController::class, 'setCurrent'])->name('semesters.setCurrent');
Route::delete('/semesters/{semester}', [SemesterController::class, 'destroy'])->name('semesters.destroy');

Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students/store', [StudentController::class, 'store'])->name('students.store');
Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
