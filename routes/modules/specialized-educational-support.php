<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpecializedEducationalSupport\{
    PersonController,
    StudentController,
    DeficiencyController,
    PositionController,
    SemesterController
};

//People

Route::get('/people', [PersonController::class, 'index'])->name('people.index');
Route::get('/people/create', [PersonController::class, 'create'])->name('people.create');
Route::post('/people/store', [PersonController::class, 'store'])->name('people.store');
Route::get('/people/{person}/edit', [PersonController::class, 'edit'])->name('people.edit');
Route::put('/people/{person}', [PersonController::class, 'update'])->name('people.update');
Route::delete('/people/{person}', [PersonController::class, 'destroy'])->name('people.destroy');

//Students

Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students/store', [StudentController::class, 'store'])->name('students.store');
Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

//Deficiencies

Route::get('/deficiencies', [DeficiencyController::class, 'index'])->name('deficiencies.index');
Route::get('/deficiencies/create', [DeficiencyController::class, 'create'])->name('deficiencies.create');
Route::post('/deficiencies/store', [DeficiencyController::class, 'store'])->name('deficiencies.store');
Route::get('/deficiencies/{deficiency}/edit', [DeficiencyController::class, 'edit'])->name('deficiencies.edit');
Route::put('/deficiencies/{deficiency}', [DeficiencyController::class, 'update'])->name('deficiencies.update');
Route::patch('/deficiencies/{deficiency}/deactivate', [DeficiencyController::class, 'toggleActive'])->name('deficiencies.deactivate');
Route::delete('/deficiencies/{deficiency}', [DeficiencyController::class, 'destroy'])->name('deficiencies.destroy');

//Positions

Route::get('/positions', [PositionController::class, 'index'])->name('positions.index');
Route::get('/positions/create', [PositionController::class, 'create'])->name('positions.create');
Route::post('/positions/store', [PositionController::class, 'store'])->name('positions.store');
Route::get('/positions/{position}/edit', [PositionController::class, 'edit'])->name('positions.edit');
Route::put('/positions/{position}', [PositionController::class, 'update'])->name('positions.update');
Route::patch('/positions/{position}/deactivate', [PositionController::class, 'toggleActive'])->name('positions.deactivate');
Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');

//Semesters

Route::get('/semesters', [SemesterController::class, 'index'])->name('semesters.index');
Route::get('/semesters/create', [SemesterController::class, 'create'])->name('semesters.create');
Route::post('/semesters/store', [SemesterController::class, 'store'])->name('semesters.store');
Route::get('/semesters/{semester}/edit', [SemesterController::class, 'edit'])->name('semesters.edit');
Route::put('/semesters/{semester}', [SemesterController::class, 'update'])->name('semesters.update');
Route::patch('/semesters/{semester}/set-current', [SemesterController::class, 'setCurrent'])->name('semesters.setCurrent');
Route::delete('/semesters/{semester}', [SemesterController::class, 'destroy'])->name('semesters.destroy');

