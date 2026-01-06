<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\AssistiveTechnologyStatusController;
use App\Http\Controllers\DeficiencyController;

Route::get('/people', [PersonController::class, 'index'])->name('people.index');
Route::get('/people/create', [PersonController::class, 'create'])->name('people.create');
Route::post('/people/store', [PersonController::class, 'store'])->name('people.store');
Route::get('/people/{person}/edit', [PersonController::class, 'edit'])->name('people.edit');
Route::put('/people/{person}', [PersonController::class, 'update'])->name('people.update');
Route::delete('/people/{person}', [PersonController::class, 'destroy'])->name('people.destroy');

Route::get('/assistive-technology-statuses', [AssistiveTechnologyStatusController::class, 'index'])->name('assistive-technology-statuses.index');
Route::get('/assistive-technology-statuses/create', [AssistiveTechnologyStatusController::class, 'create'])->name('assistive-technology-statuses.create');
Route::post('/assistive-technology-statuses/store', [AssistiveTechnologyStatusController::class, 'store'])->name('assistive-technology-statuses.store');
Route::get('/assistive-technology-statuses/{assistiveTechnologyStatus}/edit', [AssistiveTechnologyStatusController::class, 'edit'])->name('assistive-technology-statuses.edit');
Route::put('/assistive-technology-statuses/{assistiveTechnologyStatus}', [AssistiveTechnologyStatusController::class, 'update'])->name('assistive-technology-statuses.update');
Route::patch('/assistive-technology-statuses/{assistiveTechnologyStatus}/deactivate', [AssistiveTechnologyStatusController::class, 'deactivate'])->name('assistive-technology-statuses.deactivate');
Route::delete('/assistive-technology-statuses/{assistiveTechnologyStatus}', [AssistiveTechnologyStatusController::class, 'destroy'])->name('assistive-technology-statuses.destroy');

Route::get('/deficiencies', [DeficiencyController::class, 'index'])->name('deficiencies.index');
Route::get('/deficiencies/create', [DeficiencyController::class, 'create'])->name('deficiencies.create');
Route::post('/deficiencies/store', [DeficiencyController::class, 'store'])->name('deficiencies.store');
Route::get('/deficiencies/{deficiency}/edit', [DeficiencyController::class, 'edit'])->name('deficiencies.edit');
Route::put('/deficiencies/{deficiency}', [DeficiencyController::class, 'update'])->name('deficiencies.update');
Route::patch('/deficiencies/{deficiency}/deactivate', [DeficiencyController::class, 'toggleActive'])->name('deficiencies.deactivate');
Route::delete('/deficiencies/{deficiency}', [DeficiencyController::class, 'destroy'])->name('deficiencies.destroy');
