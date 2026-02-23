<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SpecializedEducationalSupport\logs\StudentLogController;
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
    PeiController,
    PeiEvaluationController,
    StudentDocumentController,

};
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    //People

    Route::get('/people', [PersonController::class, 'index'])->name('people.index');
    Route::get('/people/create', [PersonController::class, 'create'])->name('people.create');
    Route::post('/people/store', [PersonController::class, 'store'])->name('people.store');
    Route::get('/people/{person}/edit', [PersonController::class, 'edit'])->name('people.edit');
    Route::put('/people/{person}', [PersonController::class, 'update'])->name('people.update');
    Route::delete('/people/{person}', [PersonController::class, 'destroy'])->name('people.destroy');

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
    
});

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 1. STUDENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/students', [StudentController::class, 'index'])
        ->name('students.index')->middleware('can:student.index');

    Route::get('/students/{student}/show', [StudentController::class, 'show'])
        ->name('students.show')->middleware('can:student.show');

    Route::get('/students/create', [StudentController::class, 'create'])
        ->name('students.create')->middleware('can:student.create');

    Route::post('/students/store', [StudentController::class, 'store'])
        ->name('students.store')->middleware('can:student.store');

    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])
        ->name('students.edit')->middleware('can:student.edit');

    Route::put('/students/{student}', [StudentController::class, 'update'])
        ->name('students.update')->middleware('can:student.update');

    Route::delete('/students/{student}', [StudentController::class, 'destroy'])
        ->name('students.destroy')->middleware('can:student.destroy');


    /*
    |--------------------------------------------------------------------------
    | 2. GUARDIANS
    |--------------------------------------------------------------------------
    */
    Route::get('/students/{student}/guardians', [GuardianController::class, 'index'])
        ->name('guardians.index')->middleware('can:guardian.index');

    Route::get('/guardian/{guardian}/show', [GuardianController::class, 'show'])
        ->name('guardians.show')->middleware('can:guardian.show');

    Route::get('/students/{student}/guardians/create', [GuardianController::class, 'create'])
        ->name('guardians.create')->middleware('can:guardian.create');

    Route::post('/students/{student}/guardians/store', [GuardianController::class, 'store'])
        ->name('guardians.store')->middleware('can:guardian.store');

    Route::get('/students/{student}/guardians/{guardian}/edit', [GuardianController::class, 'edit'])
        ->name('guardians.edit')->middleware('can:guardian.edit');

    Route::put('/students/{student}/guardians/{guardian}', [GuardianController::class, 'update'])
        ->name('guardians.update')->middleware('can:guardian.update');

    Route::delete('/students/{student}/guardians/{guardian}', [GuardianController::class, 'destroy'])
        ->name('guardians.destroy')->middleware('can:guardian.destroy');


    /*
    |--------------------------------------------------------------------------
    | 3. PROFESSIONALS
    |--------------------------------------------------------------------------
    */
    Route::get('/professionals', [ProfessionalController::class, 'index'])
        ->name('professionals.index')->middleware('can:professional.index');

    Route::get('/professionals/{professional}/show', [ProfessionalController::class, 'show'])
        ->name('professionals.show')->middleware('can:professional.show');

    Route::get('/professionals/create', [ProfessionalController::class, 'create'])
        ->name('professionals.create')->middleware('can:professional.create');

    Route::post('/professionals/store', [ProfessionalController::class, 'store'])
        ->name('professionals.store')->middleware('can:professional.store');

    Route::get('/professionals/{professional}/edit', [ProfessionalController::class, 'edit'])
        ->name('professionals.edit')->middleware('can:professional.edit');

    Route::put('/professionals/{professional}', [ProfessionalController::class, 'update'])
        ->name('professionals.update')->middleware('can:professional.update');

    Route::delete('/professionals/{professional}', [ProfessionalController::class, 'destroy'])
        ->name('professionals.destroy')->middleware('can:professional.destroy');


    /*
    |--------------------------------------------------------------------------
    | 4. STUDENT CONTEXT
    |--------------------------------------------------------------------------
    */

    Route::get('student-context/{student}/index', [StudentContextController::class, 'index'])
        ->name('student-context.index')
        ->middleware('can:student-context.index');

    Route::get('student-context/{studentContext}/show', [StudentContextController::class, 'show'])
        ->name('student-context.show')
        ->middleware('can:student-context.show');

    Route::get('student-context/{student}/show_current', [StudentContextController::class, 'showCurrent'])
        ->name('student-context.show-current');
        // ->middleware('can:student-context.show-current');

    Route::get('student-context/{student}/create', [StudentContextController::class, 'create'])
        ->name('student-context.create')
        ->middleware('can:student-context.create');

    Route::post('student-context/{student}/store', [StudentContextController::class, 'store'])
        ->name('student-context.store')
        ->middleware('can:student-context.store');

    Route::get('student-context/{studentContext}/edit', [StudentContextController::class, 'edit'])
        ->name('student-context.edit')
        ->middleware('can:student-context.edit');

    Route::put('student-context/{studentContext}', [StudentContextController::class, 'update'])
        ->name('student-context.update')
        ->middleware('can:student-context.update');

    Route::delete('student-context/{studentContext}', [StudentContextController::class, 'destroy'])
        ->name('student-context.destroy')
        ->middleware('can:student-context.destroy');

    Route::get('student-context/{student}/new-version', [StudentContextController::class, 'makeNewVersion'])
        ->name('student-context.new-version')
        ->middleware('can:student-context.create');

    Route::post('student-context/{student}/store-new-version', [StudentContextController::class, 'storeNewVersion'])
        ->name('student-context.store-new-version')
        ->middleware('can:student-context.create');

    Route::post('student-context/{studentContext}/restore', [StudentContextController::class, 'restoreVersion'])
        ->name('student-context.restore')
        ->middleware('can:student-context.update');

    Route::get('student-context/{studentContext}/pdf', [StudentContextController::class, 'generatePdf'])
        ->name('student-context.pdf')
        ->middleware('can:student-context.pdf');


    /*
    |--------------------------------------------------------------------------
    | 5. STUDENT DEFICIENCIES
    |--------------------------------------------------------------------------
    */

    Route::get('students/{student}/deficiencies', [StudentDeficienciesController::class, 'index'])
        ->name('student-deficiencies.index')
        ->middleware('can:student-deficiency.index');

    Route::get('students/{student}/deficiencies/create', [StudentDeficienciesController::class, 'create'])
        ->name('student-deficiencies.create')
        ->middleware('can:student-deficiency.create');

    Route::post('students/{student}/deficiencies', [StudentDeficienciesController::class, 'store'])
        ->name('student-deficiencies.store')
        ->middleware('can:student-deficiency.store');

    Route::get('students/{student}/deficiencies/{student_deficiency}', [StudentDeficienciesController::class, 'show'])
        ->name('student-deficiencies.show')
        ->middleware('can:student-deficiency.show');

    Route::get('students/{student}/deficiencies/{student_deficiency}/edit', [StudentDeficienciesController::class, 'edit'])
        ->name('student-deficiencies.edit')
        ->middleware('can:student-deficiency.edit');

    Route::put('students/{student}/deficiencies/{student_deficiency}', [StudentDeficienciesController::class, 'update'])
        ->name('student-deficiencies.update')
        ->middleware('can:student-deficiency.update');

    Route::delete('students/{student}/deficiencies/{student_deficiency}', [StudentDeficienciesController::class, 'destroy'])
        ->name('student-deficiencies.destroy')
        ->middleware('can:student-deficiency.destroy');

    /*
    |--------------------------------------------------------------------------
    | 6. SESSIONS
    |--------------------------------------------------------------------------
    */
    Route::get('sessions', [SessionController::class, 'index'])
        ->name('sessions.index')->middleware('can:session.index');

    Route::get('sessions/create', [SessionController::class, 'create'])
        ->name('sessions.create')->middleware('can:session.create');

    Route::post('sessions/store', [SessionController::class, 'store'])
        ->name('sessions.store')->middleware('can:session.store');

    Route::get('sessions/{session}/show', [SessionController::class, 'show'])
        ->name('sessions.show')->middleware('can:session.show');

    Route::get('sessions/{session}/edit', [SessionController::class, 'edit'])
        ->name('sessions.edit')->middleware('can:session.edit');

    Route::put('sessions/{session}', [SessionController::class, 'update'])
        ->name('sessions.update')->middleware('can:session.update');

    Route::delete('sessions/{session}', [SessionController::class, 'destroy'])
        ->name('sessions.destroy')->middleware('can:session.destroy');

    Route::post('sessions/{session}/restore', [SessionController::class, 'restore'])
        ->name('sessions.restore')->middleware('can:session.restore');

    Route::delete('sessions/{session}/force-delete', [SessionController::class, 'forceDelete'])
        ->name('sessions.force-delete')->middleware('can:session.force-delete');

    Route::get('sessions/availability', [SessionController::class, 'availability'])
        ->name('sessions.availability');

    Route::post('sessions/{session}/cancel', [SessionController::class, 'cancel'])
        ->name('sessions.cancel')->middleware('can:session.edit'); 

    /*
    |--------------------------------------------------------------------------
    | 6.1 SESSIONS BY STUDENT (CONTEXTUALIZED)
    |--------------------------------------------------------------------------
    */
    // Listagem de sessões exclusiva de um aluno (dentro do prontuário)
    Route::get('students/{student}/sessions', [SessionController::class, 'indexByStudent'])
        ->name('students.sessions.index')->middleware('can:session.index');

    // Formulário de criação com aluno já selecionado/travado
    Route::get('students/{student}/sessions/create', [SessionController::class, 'createForStudent'])
        ->name('students.sessions.create')->middleware('can:session.create');

    /*
    |--------------------------------------------------------------------------
    | 6.2 SESSIONS BY PROFESSIONAL (MY SCHEDULE)
    |--------------------------------------------------------------------------
    */
    // Rota para o profissional logado ver apenas as suas sessões
    Route::get('my-sessions', [SessionController::class, 'mySessions'])
        ->name('sessions.my-sessions')->middleware('can:session.index');


    /*
    |--------------------------------------------------------------------------
    | 7. SESSION RECORDS
    |--------------------------------------------------------------------------
    */
    Route::get('session-records', [SessionRecordController::class, 'index'])
        ->name('session-records.index')->middleware('can:session-record.index');

    // Alterado para {session} para receber o objeto AttendanceSession no create
    Route::get('session-records/{session}/create', [SessionRecordController::class, 'create'])
        ->name('session-records.create')->middleware('can:session-record.create');

    Route::post('session-records/store', [SessionRecordController::class, 'store'])
        ->name('session-records.store')->middleware('can:session-record.store');

    Route::get('session-records/{sessionRecord}/show', [SessionRecordController::class, 'show'])
        ->name('session-records.show')->middleware('can:session-record.show');

    Route::get('session-records/{sessionRecord}/edit', [SessionRecordController::class, 'edit'])
        ->name('session-records.edit')->middleware('can:session-record.edit');

    Route::put('session-records/{sessionRecord}', [SessionRecordController::class, 'update'])
        ->name('session-records.update')->middleware('can:session-record.update');

    Route::delete('session-records/{sessionRecord}', [SessionRecordController::class, 'destroy'])
        ->name('session-records.destroy')->middleware('can:session-record.destroy');

    Route::post('session-records/{sessionRecord}/restore', [SessionRecordController::class, 'restore'])
        ->name('session-records.restore')->middleware('can:session-record.restore');

    Route::delete('session-records/{sessionRecord}/force-delete', [SessionRecordController::class, 'forceDelete'])
        ->name('session-records.force-delete')->middleware('can:session-record.force-delete');

    Route::get('session-records/{sessionRecord}/pdf', [SessionRecordController::class, 'generatePdf'])
        ->name('session-records.pdf')->middleware('can:session-record.pdf');

    /*
    |--------------------------------------------------------------------------
    | 8. STUDENT COURSES
    |--------------------------------------------------------------------------
    */
    Route::get('/student-courses/{student}/create', [StudentCourseController::class, 'create']) 
        ->name('student-courses.create')->middleware('can:student-course.create');

    Route::post('/student-courses/store/{student}', [StudentCourseController::class, 'store'])
        ->name('student-courses.store')->middleware('can:student-course.store');

    Route::get('/students/{student}/history', [StudentCourseController::class, 'index'])
        ->name('student-courses.history')->middleware('can:student-course.history');

    Route::get('student-courses/{studentCourse}', [StudentCourseController::class, 'show'])
        ->name('student-courses.show');

    Route::get('/student-courses/{studentCourse}/edit', [StudentCourseController::class, 'edit'])
        ->name('student-courses.edit')->middleware('can:student-course.edit');

    Route::put('/student-courses/{studentCourse}', [StudentCourseController::class, 'update'])
        ->name('student-courses.update')->middleware('can:student-course.update');

    Route::delete('/student-courses/{studentCourse}', [StudentCourseController::class, 'destroy'])
        ->name('student-courses.destroy')->middleware('can:student-course.destroy');


    /*
    |--------------------------------------------------------------------------
    | 9. PENDENCIES
    |--------------------------------------------------------------------------
    */
    Route::get('/pendencies', [PendencyController::class, 'index'])
        ->name('pendencies.index')->middleware('can:pendency.index');

    Route::get('/pendencies/{pendency}/show', [PendencyController::class, 'show'])
        ->name('pendencies.show')->middleware('can:pendency.show');

    Route::get('/pendencies/create', [PendencyController::class, 'create'])
        ->name('pendencies.create')->middleware('can:pendency.create');

    Route::post('/pendencies/store', [PendencyController::class, 'store'])
        ->name('pendencies.store')->middleware('can:pendency.store');

    Route::get('/pendencies/{pendency}/edit', [PendencyController::class, 'edit'])
        ->name('pendencies.edit')->middleware('can:pendency.edit');

    Route::put('/pendencies/{pendency}', [PendencyController::class, 'update'])
        ->name('pendencies.update')->middleware('can:pendency.update');

    Route::delete('/pendencies/{pendency}', [PendencyController::class, 'destroy'])
        ->name('pendencies.destroy')->middleware('can:pendency.destroy');

    Route::get('/my-pendencies', [PendencyController::class, 'myPendencies'])
        ->name('pendencies.my')->middleware('can:pendency.my');

    Route::put('/pendencies/{pendency}/complete', [PendencyController::class, 'markAsCompleted'])
        ->name('pendencies.complete')->middleware('can:pendency.complete');

    /*
    |--------------------------------------------------------------------------
    | 10. PEI (Plano Educacional Individualizado)
    |--------------------------------------------------------------------------
    */

    // Rotas Principais vinculadas ao Aluno
    Route::get('/peis', [PeiController::class, 'all'])
        ->name('pei.all');

    Route::get('/students/{student}/peis', [PeiController::class, 'index'])
        ->name('pei.index');

    Route::get('/peis/{pei}/show', [PeiController::class, 'show'])
        ->name('pei.show');

    Route::get('/students/{student}/peis/create', [PeiController::class, 'create'])
        ->name('pei.create');

    Route::post('/peis/store', [PeiController::class, 'store'])
        ->name('pei.store');

    Route::get('/peis/{pei}/edit', [PeiController::class, 'edit'])
        ->name('pei.edit');

    Route::put('/peis/{pei}', [PeiController::class, 'update'])
        ->name('pei.update');

    Route::delete('/peis/{pei}', [PeiController::class, 'destroy'])
        ->name('pei.destroy');

    Route::patch('/peis/{pei}/finish', [PeiController::class, 'finish'])
        ->name('pei.finish');

    Route::post('peis/{pei}/version', [PeiController::class, 'createVersion'])
        ->name('pei.version.newVersion');
   
    Route::post('peis/{pei}/make-current', [PeiController::class, 'makeCurrent'])
        ->name('pei.makeCurrent');

    Route::get('/peis/{pei}/pdf', [PeiController::class, 'generatePdf'])
        ->name('pei.pdf');


    /* --- Tabelas Auxiliares (Ações dentro da tela Ver PEI) --- */

    // Objetivos

    Route::get('peis/objectives/{specific_objective}', [PeiController::class, 'showObjective'])
        ->name('pei.objective.show');

    Route::get('/peis/{pei}/objectives/create', [PeiController::class, 'createObjective'])
        ->name('pei.objective.create');

    Route::post('/peis/{pei}/objectives', [PeiController::class, 'storeObjective'])
        ->name('pei.objective.store');

    Route::get('/peis/objectives/{specific_objective}/edit', [PeiController::class, 'editObjective'])
        ->name('pei.objective.edit');
    
    Route::put('/peis/objectives/{specific_objective}/update', [PeiController::class, 'updateObjective'])
        ->name('pei.objective.update');

    Route::delete('/peis/objectives/{specific_objective}', [PeiController::class, 'destroyObjective'])
        ->name('pei.objective.destroy');

    // Conteúdos
    Route::get('peis/contents/{content_programmatic}', [PeiController::class, 'showContent'])
        ->name('pei.content.show');

    Route::get('/peis/{pei}/contents/create', [PeiController::class, 'createContent'])
        ->name('pei.content.create');

    Route::post('/peis/{pei}/contents', [PeiController::class, 'storeContent'])
        ->name('pei.content.store');

    Route::get('/peis/contents/{content_programmatic}/edit', [PeiController::class, 'editContent'])
        ->name('pei.content.edit');
    
    Route::put('/peis/contents/{content_programmatic}/update', [PeiController::class, 'updateContent'])
        ->name('pei.content.update');

    Route::delete('/peis/contents/{content_programmatic}', [PeiController::class, 'destroyContent'])
        ->name('pei.content.destroy');

    // Metodologias
    Route::get('peis/methodologies/{methodology}', [PeiController::class, 'showMethodology'])
        ->name('pei.methodology.show');

    Route::get('/peis/{pei}/methodologies/create', [PeiController::class, 'createMethodology'])
        ->name('pei.methodology.create');

    Route::post('/peis/{pei}/methodologies', [PeiController::class, 'storeMethodology'])
        ->name('pei.methodology.store');

    Route::get('/peis/methodologies/{methodology}/edit', [PeiController::class, 'editMethodology'])
        ->name('pei.methodology.edit');
    
    Route::put('/peis/methodologies/{methodology}/update', [PeiController::class, 'updateMethodology'])
        ->name('pei.methodology.update');

    Route::delete('/peis/methodologies/{methodology}', [PeiController::class, 'destroyMethodology'])
        ->name('pei.methodology.destroy');

    /*
    |--------------------------------------------------------------------------
    | 11. PEI Evaluations
    |--------------------------------------------------------------------------
    */

    // Lista avaliações de um PEI
    Route::get('/peis/{pei}/evaluations', [PeiEvaluationController::class, 'index'])
        ->name('pei-evaluation.index');

    // Formulário de criação
    Route::get('/peis/{pei}/evaluations/create', [PeiEvaluationController::class, 'create'])
        ->name('pei-evaluation.create');

    // Salvar avaliação
    Route::post('/peis/{pei}/evaluations', [PeiEvaluationController::class, 'store'])
        ->name('pei-evaluation.store');

    // Visualizar avaliação específica
    Route::get('/pei-evaluations/{pei_evaluation}/show', [PeiEvaluationController::class, 'show'])
        ->name('pei-evaluation.show');

    // Editar avaliação
    Route::get('/pei-evaluations/{pei_evaluation}/edit', [PeiEvaluationController::class, 'edit'])
        ->name('pei-evaluation.edit');

    // Atualizar avaliação
    Route::put('/pei-evaluations/{pei_evaluation}', [PeiEvaluationController::class, 'update'])
        ->name('pei-evaluation.update');

    // Remover avaliação
    Route::delete('/pei-evaluations/{pei_evaluation}', [PeiEvaluationController::class, 'destroy'])
        ->name('pei-evaluation.destroy');

    // Gerar PDF da avaliação
    Route::get('/pei-evaluations/{pei_evaluation}/pdf', [PeiEvaluationController::class, 'generatePdf'])
        ->name('pei-evaluation.pdf');

    /*
    |--------------------------------------------------------------------------
    | 12. Student Documents
    |--------------------------------------------------------------------------
    */

    // Lista documentos de um aluno
    Route::get('/students/{student}/documents', [StudentDocumentController::class, 'index'])
        ->name('student-documents.index');

    // Formulário de criação (upload)
    Route::get('/students/{student}/documents/create', [StudentDocumentController::class, 'create'])
        ->name('student-documents.create');

    // Salvar documento
    Route::post('/students/{student}/documents', [StudentDocumentController::class, 'store'])
        ->name('student-documents.store');

    // Editar documento
    Route::get('/student-documents/{student_document}/edit', [StudentDocumentController::class, 'edit'])
        ->name('student-documents.edit');

    // Atualizar documento
    Route::put('/student-documents/{student_document}', [StudentDocumentController::class, 'update'])
        ->name('student-documents.update');

    // Remover documento
    Route::delete('/student-documents/{student_document}', [StudentDocumentController::class, 'destroy'])
        ->name('student-documents.destroy');

    // Download do arquivo
    Route::get('/student-documents/{student_document}/download', [StudentDocumentController::class, 'download'])
        ->name('student-documents.download');
        
    Route::get('/student-documents/{student_document}/view', [StudentDocumentController::class, 'show'])
    ->name('student-documents.view');

    /*
    |--------------------------------------------------------------------------
    | 13. Student Logs
    |--------------------------------------------------------------------------
    */

    Route::get('students/{student}/logs', [StudentLogController::class, 'index'])->name('students.logs.index');
    Route::get('students/{student}/logs/pdf', [StudentLogController::class, 'generatePdf'])->name('students.logs.pdf');

});
