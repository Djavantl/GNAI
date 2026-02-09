<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SpecializedEducationalSupport\{
    StudentDeficienciesController,
    PersonController,
    StudentController,
    StudentContextController,
    DeficiencyController,
    PositionController,
    SemesterController,
    GuardianController,
    ProfessionalController,
    SessionController,
    SessionRecordController,
    DisciplineController,
    StudentCourseController,
    CourseController,
    PendencyController,

};

Route::middleware(['auth'])->group(function () {

    //People

    Route::get('/people', [PersonController::class, 'index'])->name('people.index');
    Route::get('/people/create', [PersonController::class, 'create'])->name('people.create');
    Route::post('/people/store', [PersonController::class, 'store'])->name('people.store');
    Route::get('/people/{person}/edit', [PersonController::class, 'edit'])->name('people.edit');
    Route::put('/people/{person}', [PersonController::class, 'update'])->name('people.update');
    Route::delete('/people/{person}', [PersonController::class, 'destroy'])->name('people.destroy');


    //Students

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}/show', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students/store', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    //Deficiencies

    Route::get('/deficiencies', [DeficiencyController::class, 'index'])->name('deficiencies.index');
    Route::get('/deficiencies/{deficiency}/show', [DeficiencyController::class, 'show'])->name('deficiencies.show');
    Route::get('/deficiencies/create', [DeficiencyController::class, 'create'])->name('deficiencies.create');
    Route::post('/deficiencies/store', [DeficiencyController::class, 'store'])->name('deficiencies.store');
    Route::get('/deficiencies/{deficiency}/edit', [DeficiencyController::class, 'edit'])->name('deficiencies.edit');
    Route::put('/deficiencies/{deficiency}', [DeficiencyController::class, 'update'])->name('deficiencies.update');
    Route::patch('/deficiencies/{deficiency}/deactivate', [DeficiencyController::class, 'toggleActive'])->name('deficiencies.deactivate');
    Route::delete('/deficiencies/{deficiency}', [DeficiencyController::class, 'destroy'])->name('deficiencies.destroy');

    //Positions

    Route::get('/positions', [PositionController::class, 'index'])->name('positions.index');
    Route::get('/positions/{position}/show', [PositionController::class, 'show'])->name('positions.show');
    Route::get('/positions/create', [PositionController::class, 'create'])->name('positions.create');
    Route::post('/positions/store', [PositionController::class, 'store'])->name('positions.store');
    Route::get('/positions/{position}/edit', [PositionController::class, 'edit'])->name('positions.edit');
    Route::put('/positions/{position}', [PositionController::class, 'update'])->name('positions.update');
    Route::patch('/positions/{position}/deactivate', [PositionController::class, 'toggleActive'])->name('positions.deactivate');
    Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');

    //Semesters

    Route::get('/semesters', [SemesterController::class, 'index'])->name('semesters.index');
    Route::get('/semesters/{semester}/show', [SemesterController::class, 'show'])->name('semesters.show');
    Route::get('/semesters/create', [SemesterController::class, 'create'])->name('semesters.create');
    Route::post('/semesters/store', [SemesterController::class, 'store'])->name('semesters.store');
    Route::get('/semesters/{semester}/edit', [SemesterController::class, 'edit'])->name('semesters.edit');
    Route::put('/semesters/{semester}', [SemesterController::class, 'update'])->name('semesters.update');
    Route::patch('/semesters/{semester}/set-current', [SemesterController::class, 'setCurrent'])->name('semesters.setCurrent');
    Route::delete('/semesters/{semester}', [SemesterController::class, 'destroy'])->name('semesters.destroy');

    //Guardians

    Route::get('/students/{student}/guardians',[GuardianController::class, 'index'])->name('guardians.index');
    Route::get('/guardian/{guardian}/show',[GuardianController::class, 'show'])->name('guardians.show');
    Route::get('/students/{student}/guardians/create',[GuardianController::class, 'create'])->name('guardians.create');
    Route::post('/students/{student}/guardians/store',[GuardianController::class, 'store'])->name('guardians.store');
    Route::get('/students/{student}/guardians/{guardian}/edit',[GuardianController::class, 'edit'])->name('guardians.edit');
    Route::put('/students/{student}/guardians/{guardian}',[GuardianController::class, 'update'])->name('guardians.update');
    Route::delete('/students/{student}/guardians/{guardian}',[GuardianController::class, 'destroy'])->name('guardians.destroy');

    // Professionals

    Route::get('/professionals',[ProfessionalController::class, 'index'])->name('professionals.index');
    Route::get('/professionals/{professional}/show',[ProfessionalController::class, 'show'])->name('professionals.show');
    Route::get('/professionals/create',[ProfessionalController::class, 'create'])->name('professionals.create');
    Route::post('/professionals/store',[ProfessionalController::class, 'store'])->name('professionals.store');
    Route::get('/professionals/{professional}/edit',[ProfessionalController::class, 'edit'])->name('professionals.edit');
    Route::put('/professionals/{professional}',[ProfessionalController::class, 'update'])->name('professionals.update');
    Route::delete('/professionals/{professional}',[ProfessionalController::class, 'destroy'])->name('professionals.destroy');

    // Contexto do aluno

    Route::get('student-context/{student}/index', [StudentContextController::class, 'index'])->name('student-context.index');
    Route::get('student-context/{student_context}/show', [StudentContextController::class, 'show'])->name('student-context.show');
    Route::get('student-context/{student}/show_current', [StudentContextController::class, 'showCurrent'])->name('student-context.show-current');
    Route::post('student-context/{student_context}/set_current', [StudentContextController::class, 'setCurrent'])->name('student-context.set-current');
    Route::get('student-context/{student}/create', [StudentContextController::class, 'create'])->name('student-context.create');
    Route::post('student-context/{student}/store', [StudentContextController::class, 'store'])->name('student-context.store');
    Route::get('student-context/{student_context}/edit', [StudentContextController::class, 'edit'])->name('student-context.edit');
    Route::put('student-context/{student_context}', [StudentContextController::class, 'update'])->name('student-context.update');
    Route::delete('student-context/{student_context}', [StudentContextController::class, 'destroy'])->name('student-context.destroy');
    Route::get('/student-context/{student_context}/pdf', [StudentContextController::class, 'generatePdf'])->name('student-context.pdf');

    // Deficiencias do aluno

    Route::get('student-deficiencies/{student}', [StudentDeficienciesController::class, 'index'])->name('student-deficiencies.index');
    Route::get('student-deficiencies/{student_deficiency}/show', [StudentDeficienciesController::class, 'show'])->name('student-deficiencies.show');
    Route::get('student-deficiencies/{student}/create', [StudentDeficienciesController::class, 'create'])->name('student-deficiencies.create');
    Route::post('student-deficiencies/{student}', [StudentDeficienciesController::class, 'store'])->name('student-deficiencies.store');
    Route::get('student-deficiencies/{student_deficiency}/edit', [StudentDeficienciesController::class, 'edit'])->name('student-deficiencies.edit');
    Route::put('student-deficiencies/{student_deficiency}', [StudentDeficienciesController::class, 'update'])->name('student-deficiencies.update');
    Route::delete('student-deficiencies/{student_deficiency}', [StudentDeficienciesController::class, 'destroy'])->name('student-deficiencies.destroy');

    // Sessões de Atendimento

    Route::get('sessions', [SessionController::class, 'index'])->name('sessions.index');
    Route::get('sessions/create', [SessionController::class, 'create'])->name('sessions.create');
    Route::post('sessions/store', [SessionController::class, 'store'])->name('sessions.store');
    Route::get('sessions/{session}/show', [SessionController::class, 'show'])->name('sessions.show');
    Route::get('sessions/{session}/edit', [SessionController::class, 'edit'])->name('sessions.edit');
    Route::put('sessions/{session}', [SessionController::class, 'update'])->name('sessions.update');
    Route::delete('sessions/{session}', [SessionController::class, 'destroy'])->name('sessions.destroy');
    Route::post('sessions/{session}/restore', [SessionController::class, 'restore'])->name('sessions.restore')->withTrashed();
    Route::delete('sessions/{session}/force-delete', [SessionController::class, 'forceDelete'])->name('sessions.force-delete')->withTrashed();

    // Registros da Sessão (Session Records)

    Route::get('session-records', [SessionRecordController::class, 'index'])->name('session-records.index');
    Route::get('session-records/{session}/create', [SessionRecordController::class, 'create'])->name('session-records.create');
    Route::post('session-records/store', [SessionRecordController::class, 'store'])->name('session-records.store');
    Route::get('session-records/{sessionRecord}/show', [SessionRecordController::class, 'show'])->name('session-records.show');
    Route::get('session-records/{sessionRecord}/edit', [SessionRecordController::class, 'edit'])->name('session-records.edit');
    Route::put('session-records/{sessionRecord}', [SessionRecordController::class, 'update'])->name('session-records.update');
    Route::delete('session-records/{sessionRecord}', [SessionRecordController::class, 'destroy'])->name('session-records.destroy');
    Route::post('session-records/{sessionRecord}/restore', [SessionRecordController::class, 'restore'])->name('session-records.restore')->withTrashed();
    Route::delete('session-records/{sessionRecord}/force-delete', [SessionRecordController::class, 'forceDelete'])->name('session-records.force-delete')->withTrashed();
    Route::get('session-records/{sessionRecord}/pdf',[SessionRecordController::class, 'generatePdf'])->name('session-records.pdf');

    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}/show', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses/store', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // Disciplines
    Route::get('/disciplines', [DisciplineController::class, 'index'])->name('disciplines.index');
    Route::get('/disciplines/{discipline}/show', [DisciplineController::class, 'show'])->name('disciplines.show');
    Route::get('/disciplines/create', [DisciplineController::class, 'create'])->name('disciplines.create');
    Route::post('/disciplines/store', [DisciplineController::class, 'store'])->name('disciplines.store');
    Route::get('/disciplines/{discipline}/edit', [DisciplineController::class, 'edit'])->name('disciplines.edit');
    Route::put('/disciplines/{discipline}', [DisciplineController::class, 'update'])->name('disciplines.update');
    Route::delete('/disciplines/{discipline}', [DisciplineController::class, 'destroy'])->name('disciplines.destroy');

    // Student Courses (Enrollments & History)
    Route::get('/student-courses', [StudentCourseController::class, 'create'])->name('student-courses.create');
    Route::post('/student-courses/store', [StudentCourseController::class, 'store'])->name('student-courses.store');
    Route::get('/students/{student}/history', [StudentCourseController::class, 'index'])->name('student-courses.history');
    Route::get('/student-courses/{studentCourse}/edit', [StudentCourseController::class, 'edit'])->name('student-courses.edit');
    Route::put('/student-courses/{studentCourse}', [StudentCourseController::class, 'update'])->name('student-courses.update');
    Route::delete('/student-courses/{studentCourse}', [StudentCourseController::class, 'destroy'])->name('student-courses.destroy');

    // Pendencies
    Route::get('/pendencies', [PendencyController::class, 'index'])->name('pendencies.index');
    Route::get('/pendencies/{pendency}/show', [PendencyController::class, 'show'])->name('pendencies.show');
    Route::get('/pendencies/create', [PendencyController::class, 'create'])->name('pendencies.create');
    Route::post('/pendencies/store', [PendencyController::class, 'store'])->name('pendencies.store');
    Route::get('/pendencies/{pendency}/edit', [PendencyController::class, 'edit'])->name('pendencies.edit');
    Route::put('/pendencies/{pendency}', [PendencyController::class, 'update'])->name('pendencies.update');
    Route::delete('/pendencies/{pendency}', [PendencyController::class, 'destroy'])->name('pendencies.destroy');
    Route::get('/my-pendencies', [PendencyController::class, 'myPendencies'])->name('pendencies.my');
    Route::put('/pendencies/{pendency}/complete', [PendencyController::class, 'markAsCompleted'])->name('pendencies.complete');
});
