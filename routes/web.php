<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\AssistiveTechnologyStatusController;
use App\Http\Controllers\DeficiencyController;
use App\Http\Controllers\AssistiveTechnologyController;

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
