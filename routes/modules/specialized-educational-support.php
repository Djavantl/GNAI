<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpecializedEducationalSupport\logs\StudentLogController;
use App\Http\Controllers\SpecializedEducationalSupport\{
    StudentDeficienciesController, PersonController, StudentController, StudentContextController,
    DeficiencyController, PositionController, SemesterController, GuardianController,
    ProfessionalController, SessionController, SessionRecordController, DisciplineController,
    StudentCourseController, CourseController, PendencyController, PeiController,
    PeiEvaluationController, StudentDocumentController, TeacherController
};

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // People
    Route::get('/people', [PersonController::class, 'index'])->name('people.index')->middleware('can:people.view');
    Route::get('/people/create', [PersonController::class, 'create'])->name('people.create')->middleware('can:people.create');
    Route::post('/people/store', [PersonController::class, 'store'])->name('people.store')->middleware('can:people.create');
    Route::get('/people/{person}/edit', [PersonController::class, 'edit'])->name('people.edit')->middleware('can:people.update');
    Route::put('/people/{person}', [PersonController::class, 'update'])->name('people.update')->middleware('can:people.update');
    Route::delete('/people/{person}', [PersonController::class, 'destroy'])->name('people.destroy')->middleware('can:people.delete');

    // Deficiencies
    Route::get('/deficiencies', [DeficiencyController::class, 'index'])->name('deficiencies.index')->middleware('can:deficiency.view');
    Route::get('/deficiencies/{deficiency}/show', [DeficiencyController::class, 'show'])->name('deficiencies.show')->middleware('can:deficiency.view');
    Route::get('/deficiencies/create', [DeficiencyController::class, 'create'])->name('deficiencies.create')->middleware('can:deficiency.create');
    Route::post('/deficiencies/store', [DeficiencyController::class, 'store'])->name('deficiencies.store')->middleware('can:deficiency.create');
    Route::get('/deficiencies/{deficiency}/edit', [DeficiencyController::class, 'edit'])->name('deficiencies.edit')->middleware('can:deficiency.update');
    Route::put('/deficiencies/{deficiency}', [DeficiencyController::class, 'update'])->name('deficiencies.update')->middleware('can:deficiency.update');
    Route::patch('/deficiencies/{deficiency}/deactivate', [DeficiencyController::class, 'toggleActive'])->name('deficiencies.deactivate')->middleware('can:deficiency.update');
    Route::delete('/deficiencies/{deficiency}', [DeficiencyController::class, 'destroy'])->name('deficiencies.destroy')->middleware('can:deficiency.delete');

    // Positions
    Route::get('/positions', [PositionController::class, 'index'])->name('positions.index')->middleware('can:position.view');
    Route::get('/positions/{position}/show', [PositionController::class, 'show'])->name('positions.show')->middleware('can:position.view');
    Route::get('/positions/create', [PositionController::class, 'create'])->name('positions.create')->middleware('can:position.create');
    Route::post('/positions/store', [PositionController::class, 'store'])->name('positions.store')->middleware('can:position.create');
    Route::get('/positions/{position}/edit', [PositionController::class, 'edit'])->name('positions.edit')->middleware('can:position.update');
    Route::put('/positions/{position}', [PositionController::class, 'update'])->name('positions.update')->middleware('can:position.update');
    Route::patch('/positions/{position}/deactivate', [PositionController::class, 'toggleActive'])->name('positions.deactivate')->middleware('can:position.update');
    Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy')->middleware('can:position.delete');

    // Semesters
    Route::get('/semesters', [SemesterController::class, 'index'])->name('semesters.index')->middleware('can:semester.view');
    Route::get('/semesters/{semester}/show', [SemesterController::class, 'show'])->name('semesters.show')->middleware('can:semester.view');
    Route::get('/semesters/create', [SemesterController::class, 'create'])->name('semesters.create')->middleware('can:semester.create');
    Route::post('/semesters/store', [SemesterController::class, 'store'])->name('semesters.store')->middleware('can:semester.create');
    Route::get('/semesters/{semester}/edit', [SemesterController::class, 'edit'])->name('semesters.edit')->middleware('can:semester.update');
    Route::put('/semesters/{semester}', [SemesterController::class, 'update'])->name('semesters.update')->middleware('can:semester.update');
    Route::patch('/semesters/{semester}/set-current', [SemesterController::class, 'setCurrent'])->name('semesters.setCurrent')->middleware('can:semester.update');
    Route::delete('/semesters/{semester}', [SemesterController::class, 'destroy'])->name('semesters.destroy')->middleware('can:semester.delete');

    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index')->middleware('can:course.view');
    Route::get('/courses/{course}/show', [CourseController::class, 'show'])->name('courses.show')->middleware('can:course.view');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create')->middleware('can:course.create');
    Route::post('/courses/store', [CourseController::class, 'store'])->name('courses.store')->middleware('can:course.create');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit')->middleware('can:course.update');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update')->middleware('can:course.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy')->middleware('can:course.delete');

    // Disciplines
    Route::get('/disciplines', [DisciplineController::class, 'index'])->name('disciplines.index')->middleware('can:discipline.view');
    Route::get('/disciplines/{discipline}/show', [DisciplineController::class, 'show'])->name('disciplines.show')->middleware('can:discipline.view');
    Route::get('/disciplines/create', [DisciplineController::class, 'create'])->name('disciplines.create')->middleware('can:discipline.create');
    Route::post('/disciplines/store', [DisciplineController::class, 'store'])->name('disciplines.store')->middleware('can:discipline.create');
    Route::get('/disciplines/{discipline}/edit', [DisciplineController::class, 'edit'])->name('disciplines.edit')->middleware('can:discipline.update');
    Route::put('/disciplines/{discipline}', [DisciplineController::class, 'update'])->name('disciplines.update')->middleware('can:discipline.update');
    Route::delete('/disciplines/{discipline}', [DisciplineController::class, 'destroy'])->name('disciplines.destroy')->middleware('can:discipline.delete');
});

Route::middleware(['auth'])->group(function () {

    /* 1. STUDENTS */
    Route::get('/students', [StudentController::class, 'index'])->name('students.index')->middleware('can:student.view');
    Route::get('/students/{student}/show', [StudentController::class, 'show'])->name('students.show')->middleware('can:student.view');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create')->middleware('can:student.create');
    Route::post('/students/store', [StudentController::class, 'store'])->name('students.store')->middleware('can:student.create');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit')->middleware('can:student.update');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update')->middleware('can:student.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy')->middleware('can:student.delete');

    /* 2. GUARDIANS */
    Route::get('/students/{student}/guardians', [GuardianController::class, 'index'])->name('guardians.index')->middleware('can:guardian.view');
    Route::get('/guardian/{guardian}/show', [GuardianController::class, 'show'])->name('guardians.show')->middleware('can:guardian.view');
    Route::get('/students/{student}/guardians/create', [GuardianController::class, 'create'])->name('guardians.create')->middleware('can:guardian.create');
    Route::post('/students/{student}/guardians/store', [GuardianController::class, 'store'])->name('guardians.store')->middleware('can:guardian.create');
    Route::get('/students/{student}/guardians/{guardian}/edit', [GuardianController::class, 'edit'])->name('guardians.edit')->middleware('can:guardian.update');
    Route::put('/students/{student}/guardians/{guardian}', [GuardianController::class, 'update'])->name('guardians.update')->middleware('can:guardian.update');
    Route::delete('/students/{student}/guardians/{guardian}', [GuardianController::class, 'destroy'])->name('guardians.destroy')->middleware('can:guardian.delete');

    /* 3. PROFESSIONALS */
    Route::get('/professionals', [ProfessionalController::class, 'index'])->name('professionals.index')->middleware('can:professional.view');
    Route::get('/professionals/{professional}/show', [ProfessionalController::class, 'show'])->name('professionals.show')->middleware('can:professional.view');
    Route::get('/professionals/create', [ProfessionalController::class, 'create'])->name('professionals.create')->middleware('can:professional.create');
    Route::post('/professionals/store', [ProfessionalController::class, 'store'])->name('professionals.store')->middleware('can:professional.create');
    Route::get('/professionals/{professional}/edit', [ProfessionalController::class, 'edit'])->name('professionals.edit')->middleware('can:professional.update');
    Route::put('/professionals/{professional}', [ProfessionalController::class, 'update'])->name('professionals.update')->middleware('can:professional.update');
    Route::delete('/professionals/{professional}', [ProfessionalController::class, 'destroy'])->name('professionals.destroy')->middleware('can:professional.delete');

    /* 4. TEACHERS */
    Route::get('/teachers/permissions', [TeacherController::class, 'permissions'])->name('teachers.permissions')->middleware('can:teacher.view');
    Route::put('/teachers/permissions/update', [TeacherController::class, 'updatePermissions'])->name('teachers.permissions.update')->middleware('can:teacher.update');
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index')->middleware('can:teacher.view');
    Route::get('/teachers/{teacher}/show', [TeacherController::class, 'show'])->name('teachers.show')->middleware('can:teacher.view');
    Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create')->middleware('can:teacher.create');
    Route::post('/teachers/store', [TeacherController::class, 'store'])->name('teachers.store')->middleware('can:teacher.create');
    Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit')->middleware('can:teacher.update');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update')->middleware('can:teacher.update');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy')->middleware('can:teacher.delete');
    Route::get('/teachers/{teacher}/disciplines', [TeacherController::class, 'disciplines'])->name('teachers.disciplines')->middleware('can:teacher.view');
    Route::put('/teachers/{teacher}/disciplines', [TeacherController::class, 'updateDisciplines'])->name('teachers.disciplines.update')->middleware('can:teacher.update');

    /* 5. STUDENT CONTEXT */
    Route::get('student-context/{student}/index', [StudentContextController::class, 'index'])->name('student-context.index')->middleware('can:student-context.view');
    Route::get('student-context/{studentContext}/show', [StudentContextController::class, 'show'])->name('student-context.show')->middleware('can:student-context.view');
    Route::get('student-context/{student}/show_current', [StudentContextController::class, 'showCurrent'])->name('student-context.show-current')->middleware('can:student-context.view');
    Route::get('student-context/{student}/create', [StudentContextController::class, 'create'])->name('student-context.create')->middleware('can:student-context.create');
    Route::post('student-context/{student}/store', [StudentContextController::class, 'store'])->name('student-context.store')->middleware('can:student-context.create');
    Route::get('student-context/{studentContext}/edit', [StudentContextController::class, 'edit'])->name('student-context.edit')->middleware('can:student-context.update');
    Route::put('student-context/{studentContext}', [StudentContextController::class, 'update'])->name('student-context.update')->middleware('can:student-context.update');
    Route::delete('student-context/{studentContext}', [StudentContextController::class, 'destroy'])->name('student-context.destroy')->middleware('can:student-context.delete');
    Route::get('student-context/{student}/new-version', [StudentContextController::class, 'makeNewVersion'])->name('student-context.new-version')->middleware('can:student-context.create');
    Route::post('student-context/{student}/store-new-version', [StudentContextController::class, 'storeNewVersion'])->name('student-context.store-new-version')->middleware('can:student-context.create');
    Route::post('student-context/{studentContext}/restore', [StudentContextController::class, 'restoreVersion'])->name('student-context.restore')->middleware('can:student-context.update');
    Route::get('student-context/{studentContext}/pdf', [StudentContextController::class, 'generatePdf'])->name('student-context.pdf')->middleware('can:student-context.view');

    /* 6. STUDENT DEFICIENCIES */
    Route::get('students/{student}/deficiencies', [StudentDeficienciesController::class, 'index'])->name('student-deficiencies.index')->middleware('can:student-deficiency.view');
    Route::get('students/{student}/deficiencies/create', [StudentDeficienciesController::class, 'create'])->name('student-deficiencies.create')->middleware('can:student-deficiency.create');
    Route::post('students/{student}/deficiencies', [StudentDeficienciesController::class, 'store'])->name('student-deficiencies.store')->middleware('can:student-deficiency.create');
    Route::get('students/{student}/deficiencies/{student_deficiency}', [StudentDeficienciesController::class, 'show'])->name('student-deficiencies.show')->middleware('can:student-deficiency.view');
    Route::get('students/{student}/deficiencies/{student_deficiency}/edit', [StudentDeficienciesController::class, 'edit'])->name('student-deficiencies.edit')->middleware('can:student-deficiency.update');
    Route::put('students/{student}/deficiencies/{student_deficiency}', [StudentDeficienciesController::class, 'update'])->name('student-deficiencies.update')->middleware('can:student-deficiency.update');
    Route::delete('students/{student}/deficiencies/{student_deficiency}', [StudentDeficienciesController::class, 'destroy'])->name('student-deficiencies.destroy')->middleware('can:student-deficiency.delete');

    /* 7. SESSIONS */
    Route::get('sessions', [SessionController::class, 'index'])->name('sessions.index')->middleware('can:session.view');
    Route::get('sessions/create', [SessionController::class, 'create'])->name('sessions.create')->middleware('can:session.create');
    Route::post('sessions/store', [SessionController::class, 'store'])->name('sessions.store')->middleware('can:session.create');
    Route::get('sessions/{session}/show', [SessionController::class, 'show'])->name('sessions.show')->middleware('can:session.view');
    Route::get('sessions/{session}/edit', [SessionController::class, 'edit'])->name('sessions.edit')->middleware('can:session.update');
    Route::put('sessions/{session}', [SessionController::class, 'update'])->name('sessions.update')->middleware('can:session.update');
    Route::delete('sessions/{session}', [SessionController::class, 'destroy'])->name('sessions.destroy')->middleware('can:session.delete');
    Route::post('sessions/{session}/restore', [SessionController::class, 'restore'])->name('sessions.restore')->middleware('can:session.update');
    Route::delete('sessions/{session}/force-delete', [SessionController::class, 'forceDelete'])->name('sessions.force-delete')->middleware('can:session.delete');
    Route::get('sessions/availability', [SessionController::class, 'availability'])->name('sessions.availability')->middleware('can:session.view');
    Route::post('sessions/{session}/cancel', [SessionController::class, 'cancel'])->name('sessions.cancel')->middleware('can:session.update');
    Route::get('students/{student}/sessions', [SessionController::class, 'indexByStudent'])->name('students.sessions.index')->middleware('can:session.view');
    Route::get('students/{student}/sessions/create', [SessionController::class, 'createForStudent'])->name('students.sessions.create')->middleware('can:session.create');
    Route::get('my-sessions', [SessionController::class, 'mySessions'])->name('sessions.my-sessions')->middleware('can:session.view');

    /* 8. SESSION RECORDS */
    Route::get('session-records', [SessionRecordController::class, 'index'])->name('session-records.index')->middleware('can:session-record.view');
    Route::get('session-records/{session}/create', [SessionRecordController::class, 'create'])->name('session-records.create')->middleware('can:session-record.create');
    Route::post('session-records/store', [SessionRecordController::class, 'store'])->name('session-records.store')->middleware('can:session-record.create');
    Route::get('session-records/{sessionRecord}/show', [SessionRecordController::class, 'show'])->name('session-records.show')->middleware('can:session-record.view');
    Route::get('session-records/{sessionRecord}/edit', [SessionRecordController::class, 'edit'])->name('session-records.edit')->middleware('can:session-record.update');
    Route::put('session-records/{sessionRecord}', [SessionRecordController::class, 'update'])->name('session-records.update')->middleware('can:session-record.update');
    Route::delete('session-records/{sessionRecord}', [SessionRecordController::class, 'destroy'])->name('session-records.destroy')->middleware('can:session-record.delete');
    Route::post('session-records/{sessionRecord}/restore', [SessionRecordController::class, 'restore'])->name('session-records.restore')->middleware('can:session-record.update');
    Route::delete('session-records/{sessionRecord}/force-delete', [SessionRecordController::class, 'forceDelete'])->name('session-records.force-delete')->middleware('can:session-record.delete');
    Route::get('session-records/{sessionRecord}/pdf', [SessionRecordController::class, 'generatePdf'])->name('session-records.pdf')->middleware('can:session-record.view');

    /* 9. STUDENT COURSES */
    Route::get('/student-courses/{student}/create', [StudentCourseController::class, 'create'])->name('student-courses.create')->middleware('can:student-course.create');
    Route::post('/student-courses/store/{student}', [StudentCourseController::class, 'store'])->name('student-courses.store')->middleware('can:student-course.create');
    Route::get('/students/{student}/history', [StudentCourseController::class, 'index'])->name('student-courses.history')->middleware('can:student-course.view');
    Route::get('student-courses/{studentCourse}', [StudentCourseController::class, 'show'])->name('student-courses.show')->middleware('can:student-course.view');
    Route::get('/student-courses/{studentCourse}/edit', [StudentCourseController::class, 'edit'])->name('student-courses.edit')->middleware('can:student-course.update');
    Route::put('/student-courses/{studentCourse}', [StudentCourseController::class, 'update'])->name('student-courses.update')->middleware('can:student-course.update');
    Route::delete('/student-courses/{studentCourse}', [StudentCourseController::class, 'destroy'])->name('student-courses.destroy')->middleware('can:student-course.delete');

    /* 10. PENDENCIES */
    Route::get('/pendencies', [PendencyController::class, 'index'])->name('pendencies.index')->middleware('can:pendency.view');
    Route::get('/pendencies/{pendency}/show', [PendencyController::class, 'show'])->name('pendencies.show')->middleware('can:pendency.view');
    Route::get('/pendencies/create', [PendencyController::class, 'create'])->name('pendencies.create')->middleware('can:pendency.create');
    Route::post('/pendencies/store', [PendencyController::class, 'store'])->name('pendencies.store')->middleware('can:pendency.create');
    Route::get('/pendencies/{pendency}/edit', [PendencyController::class, 'edit'])->name('pendencies.edit')->middleware('can:pendency.update');
    Route::put('/pendencies/{pendency}', [PendencyController::class, 'update'])->name('pendencies.update')->middleware('can:pendency.update');
    Route::delete('/pendencies/{pendency}', [PendencyController::class, 'destroy'])->name('pendencies.destroy')->middleware('can:pendency.delete');
    Route::get('/my-pendencies', [PendencyController::class, 'myPendencies'])->name('pendencies.my')->middleware('can:pendency.view');
    Route::put('/pendencies/{pendency}/complete', [PendencyController::class, 'markAsCompleted'])->name('pendencies.complete')->middleware('can:pendency.update');

    /* 11. PEI */
    Route::get('/peis', [PeiController::class, 'all'])->name('pei.all')->middleware('can:pei.view');
    Route::get('/students/{student}/peis', [PeiController::class, 'index'])->name('pei.index')->middleware('can:pei.view');
    Route::get('/peis/{pei}/show', [PeiController::class, 'show'])->name('pei.show')->middleware('can:pei.view');
    Route::get('/students/{student}/peis/create', [PeiController::class, 'create'])->name('pei.create')->middleware('can:pei.create');
    Route::post('/peis/store', [PeiController::class, 'store'])->name('pei.store')->middleware('can:pei.create');
    Route::get('/peis/{pei}/edit', [PeiController::class, 'edit'])->name('pei.edit')->middleware('can:pei.update');
    Route::put('/peis/{pei}', [PeiController::class, 'update'])->name('pei.update')->middleware('can:pei.update');
    Route::delete('/peis/{pei}', [PeiController::class, 'destroy'])->name('pei.destroy')->middleware('can:pei.delete');
    Route::patch('/peis/{pei}/finish', [PeiController::class, 'finish'])->name('pei.finish')->middleware('can:pei.update');
    Route::post('peis/{pei}/version', [PeiController::class, 'createVersion'])->name('pei.version.newVersion')->middleware('can:pei.create');
    Route::post('peis/{pei}/make-current', [PeiController::class, 'makeCurrent'])->name('pei.makeCurrent')->middleware('can:pei.update');
    Route::get('/peis/{pei}/pdf', [PeiController::class, 'generatePdf'])->name('pei.pdf')->middleware('can:pei.view');

    // PEI Auxiliares (Objetivos, Conteúdos, Metodologias) - Agrupados na permissão PEI
    Route::get('peis/objectives/{specific_objective}', [PeiController::class, 'showObjective'])->name('pei.objective.show')->middleware('can:pei.view');
    Route::get('/peis/{pei}/objectives/create', [PeiController::class, 'createObjective'])->name('pei.objective.create')->middleware('can:pei.create');
    Route::post('/peis/{pei}/objectives', [PeiController::class, 'storeObjective'])->name('pei.objective.store')->middleware('can:pei.create');
    Route::get('/peis/objectives/{specific_objective}/edit', [PeiController::class, 'editObjective'])->name('pei.objective.edit')->middleware('can:pei.update');
    Route::put('/peis/objectives/{specific_objective}/update', [PeiController::class, 'updateObjective'])->name('pei.objective.update')->middleware('can:pei.update');
    Route::delete('/peis/objectives/{specific_objective}', [PeiController::class, 'destroyObjective'])->name('pei.objective.destroy')->middleware('can:pei.delete');

    Route::get('peis/contents/{content_programmatic}', [PeiController::class, 'showContent'])->name('pei.content.show')->middleware('can:pei.view');
    Route::get('/peis/{pei}/contents/create', [PeiController::class, 'createContent'])->name('pei.content.create')->middleware('can:pei.create');
    Route::post('/peis/{pei}/contents', [PeiController::class, 'storeContent'])->name('pei.content.store')->middleware('can:pei.create');
    Route::get('/peis/contents/{content_programmatic}/edit', [PeiController::class, 'editContent'])->name('pei.content.edit')->middleware('can:pei.update');
    Route::put('/peis/contents/{content_programmatic}/update', [PeiController::class, 'updateContent'])->name('pei.content.update')->middleware('can:pei.update');
    Route::delete('/peis/contents/{content_programmatic}', [PeiController::class, 'destroyContent'])->name('pei.content.destroy')->middleware('can:pei.delete');

    Route::get('peis/methodologies/{methodology}', [PeiController::class, 'showMethodology'])->name('pei.methodology.show')->middleware('can:pei.view');
    Route::get('/peis/{pei}/methodologies/create', [PeiController::class, 'createMethodology'])->name('pei.methodology.create')->middleware('can:pei.create');
    Route::post('/peis/{pei}/methodologies', [PeiController::class, 'storeMethodology'])->name('pei.methodology.store')->middleware('can:pei.create');
    Route::get('/peis/methodologies/{methodology}/edit', [PeiController::class, 'editMethodology'])->name('pei.methodology.edit')->middleware('can:pei.update');
    Route::put('/peis/methodologies/{methodology}/update', [PeiController::class, 'updateMethodology'])->name('pei.methodology.update')->middleware('can:pei.update');
    Route::delete('/peis/methodologies/{methodology}', [PeiController::class, 'destroyMethodology'])->name('pei.methodology.destroy')->middleware('can:pei.delete');

    /* 12. PEI EVALUATIONS */
    Route::get('/peis/{pei}/evaluations', [PeiEvaluationController::class, 'index'])->name('pei-evaluation.index')->middleware('can:pei-evaluation.view');
    Route::get('/peis/{pei}/evaluations/create', [PeiEvaluationController::class, 'create'])->name('pei-evaluation.create')->middleware('can:pei-evaluation.create');
    Route::post('/peis/{pei}/evaluations', [PeiEvaluationController::class, 'store'])->name('pei-evaluation.store')->middleware('can:pei-evaluation.create');
    Route::get('/pei-evaluations/{pei_evaluation}/show', [PeiEvaluationController::class, 'show'])->name('pei-evaluation.show')->middleware('can:pei-evaluation.view');
    Route::get('/pei-evaluations/{pei_evaluation}/edit', [PeiEvaluationController::class, 'edit'])->name('pei-evaluation.edit')->middleware('can:pei-evaluation.update');
    Route::put('/pei-evaluations/{pei_evaluation}', [PeiEvaluationController::class, 'update'])->name('pei-evaluation.update')->middleware('can:pei-evaluation.update');
    Route::delete('/pei-evaluations/{pei_evaluation}', [PeiEvaluationController::class, 'destroy'])->name('pei-evaluation.destroy')->middleware('can:pei-evaluation.delete');
    Route::get('/pei-evaluations/{pei_evaluation}/pdf', [PeiEvaluationController::class, 'generatePdf'])->name('pei-evaluation.pdf')->middleware('can:pei-evaluation.view');

    /* 13. STUDENT DOCUMENTS */
    Route::get('/students/{student}/documents', [StudentDocumentController::class, 'index'])->name('student-documents.index')->middleware('can:student-document.view');
    Route::get('/students/{student}/documents/create', [StudentDocumentController::class, 'create'])->name('student-documents.create')->middleware('can:student-document.create');
    Route::post('/students/{student}/documents', [StudentDocumentController::class, 'store'])->name('student-documents.store')->middleware('can:student-document.create');
    Route::get('/student-documents/{student_document}/edit', [StudentDocumentController::class, 'edit'])->name('student-documents.edit')->middleware('can:student-document.update');
    Route::put('/student-documents/{student_document}', [StudentDocumentController::class, 'update'])->name('student-documents.update')->middleware('can:student-document.update');
    Route::delete('/student-documents/{student_document}', [StudentDocumentController::class, 'destroy'])->name('student-documents.destroy')->middleware('can:student-document.delete');
    Route::get('/student-documents/{student_document}/download', [StudentDocumentController::class, 'download'])->name('student-documents.download')->middleware('can:student-document.view');
    Route::get('/student-documents/{student_document}/view', [StudentDocumentController::class, 'show'])->name('student-documents.view')->middleware('can:student-document.view');

    /* 14. STUDENT LOGS (Sem middleware de permissão conforme solicitado) */
    Route::get('students/{student}/logs', [StudentLogController::class, 'index'])->name('students.logs.index');
    Route::get('students/{student}/logs/pdf', [StudentLogController::class, 'generatePdf'])->name('students.logs.pdf');

});