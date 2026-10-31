<?php

use App\Domains\Inclusion\UI\Controllers\AccessibilityFeatureController;
use App\Domains\Inclusion\UI\Controllers\AeeRecordController;
use App\Domains\Inclusion\UI\Controllers\BarrierCategoryController;
use App\Domains\Inclusion\UI\Controllers\BarrierController;
use App\Domains\Inclusion\UI\Controllers\CourseController;
use App\Domains\Inclusion\UI\Controllers\DeficiencyController;
use App\Domains\Inclusion\UI\Controllers\DisciplineController;
use App\Domains\Inclusion\UI\Controllers\GuardianController;
use App\Domains\Inclusion\UI\Controllers\PublicationController;
use App\Domains\Inclusion\UI\Controllers\InstitutionController;
use App\Domains\Inclusion\UI\Controllers\ItemController;
use App\Domains\Inclusion\UI\Controllers\LoanController;
use App\Domains\Inclusion\UI\Controllers\LocationController;
use App\Domains\Inclusion\UI\Controllers\PedagogicalRecordController;
use App\Domains\Inclusion\UI\Controllers\PeiController;
use App\Domains\Inclusion\UI\Controllers\PendencyController;
use App\Domains\Inclusion\UI\Controllers\PositionController;
use App\Domains\Inclusion\UI\Controllers\ProfessionalController;
use App\Domains\Inclusion\UI\Controllers\SemesterController;
use App\Domains\Inclusion\UI\Controllers\SessionController;
use App\Domains\Inclusion\UI\Controllers\StudentContextController;
use App\Domains\Inclusion\UI\Controllers\StudentController;
use App\Domains\Inclusion\UI\Controllers\StudentCourseController;
use App\Domains\Inclusion\UI\Controllers\StudentDeficiencyController;
use App\Domains\Inclusion\UI\Controllers\StudentDocumentController;
use App\Domains\Inclusion\UI\Controllers\TagController;
use App\Domains\Inclusion\UI\Controllers\TeacherController;
use App\Domains\Inclusion\UI\Controllers\WaitlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Inclusão: radar inclusivo e atendimento educacional especializado
|--------------------------------------------------------------------------
| Um único domínio (App\Domains\Inclusion). Os prefixos de URL e de nome
| das rotas foram mantidos para preservar links, permissões e notificações
| já gravadas.
*/

Route::prefix('inclusive-radar')->name('inclusive-radar.')->group(function (): void {
    /*
    |--------------------------------------------------------------------------
    | ADMIN – Gestão de Cadastros (somente administradores)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {});

    /*
    |--------------------------------------------------------------------------
    | OPERACIONAL – Recursos e Ações do Dia a Dia (autenticado + permissões)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])->group(function () {
        // ------------------- CATEGORIAS DAS BARREIRAS -------------------
        Route::get('/barrier-categories', [BarrierCategoryController::class, 'index'])
            ->name('barrier-categories.index')->middleware('can:barrier-category.index');
        Route::get('/barrier-categories/create', [BarrierCategoryController::class, 'create'])
            ->name('barrier-categories.create')->middleware('can:barrier-category.create');
        Route::post('/barrier-categories/store', [BarrierCategoryController::class, 'store'])
            ->name('barrier-categories.store')->middleware('can:barrier-category.store');
        Route::get('/barrier-categories/{barrierCategory}', [BarrierCategoryController::class, 'show'])
            ->name('barrier-categories.show')->middleware('can:barrier-category.show');
        Route::get('/barrier-categories/{barrierCategory}/edit', [BarrierCategoryController::class, 'edit'])
            ->name('barrier-categories.edit')->middleware('can:barrier-category.edit');
        Route::put('/barrier-categories/{barrierCategory}', [BarrierCategoryController::class, 'update'])
            ->name('barrier-categories.update')->middleware('can:barrier-category.update');
        Route::delete('/barrier-categories/{barrierCategory}', [BarrierCategoryController::class, 'destroy'])
            ->name('barrier-categories.destroy')->middleware('can:barrier-category.destroy');

        // ------------------- INSTITUIÇÕES -------------------
        Route::get('/institutions', [InstitutionController::class, 'index'])
            ->name('institutions.index')->middleware('can:institution.index');
        Route::get('/institutions/create', [InstitutionController::class, 'create'])
            ->name('institutions.create')->middleware('can:institution.create');
        Route::post('/institutions/store', [InstitutionController::class, 'store'])
            ->name('institutions.store')->middleware('can:institution.store');
        Route::get('/institutions/{institution}', [InstitutionController::class, 'show'])
            ->name('institutions.show')->middleware('can:institution.show');
        Route::get('/institutions/{institution}/edit', [InstitutionController::class, 'edit'])
            ->name('institutions.edit')->middleware('can:institution.edit');
        Route::put('/institutions/{institution}', [InstitutionController::class, 'update'])
            ->name('institutions.update')->middleware('can:institution.update');
        Route::delete('/institutions/{institution}', [InstitutionController::class, 'destroy'])
            ->name('institutions.destroy')->middleware('can:institution.destroy');

        // ------------------- LOCALIZAÇÕES -------------------
        Route::get('/locations', [LocationController::class, 'index'])
            ->name('locations.index')->middleware('can:location.index');
        Route::get('/locations/create', [LocationController::class, 'create'])
            ->name('locations.create')->middleware('can:location.create');
        Route::post('/locations/store', [LocationController::class, 'store'])
            ->name('locations.store')->middleware('can:location.store');
        Route::get('/locations/{location}', [LocationController::class, 'show'])
            ->name('locations.show')->middleware('can:location.show');
        Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])
            ->name('locations.edit')->middleware('can:location.edit');
        Route::put('/locations/{location}', [LocationController::class, 'update'])
            ->name('locations.update')->middleware('can:location.update');
        Route::delete('/locations/{location}', [LocationController::class, 'destroy'])
            ->name('locations.destroy')->middleware('can:location.destroy');

        // ------------------- RECURSOS DE ACESSIBILIDADE -------------------
        Route::get('/accessibility-features', [AccessibilityFeatureController::class, 'index'])
            ->name('accessibility-features.index')->middleware('can:accessibility-feature.index');
        Route::get('/accessibility-features/create', [AccessibilityFeatureController::class, 'create'])
            ->name('accessibility-features.create')->middleware('can:accessibility-feature.create');
        Route::post('/accessibility-features/store', [AccessibilityFeatureController::class, 'store'])
            ->name('accessibility-features.store')->middleware('can:accessibility-feature.store');
        Route::get('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'show'])
            ->name('accessibility-features.show')->middleware('can:accessibility-feature.show');
        Route::get('/accessibility-features/{accessibilityFeature}/edit', [AccessibilityFeatureController::class, 'edit'])
            ->name('accessibility-features.edit')->middleware('can:accessibility-feature.edit');
        Route::put('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'update'])
            ->name('accessibility-features.update')->middleware('can:accessibility-feature.update');
        Route::delete('/accessibility-features/{accessibilityFeature}', [AccessibilityFeatureController::class, 'destroy'])
            ->name('accessibility-features.destroy')->middleware('can:accessibility-feature.destroy');

        // ------------------- ETIQUETAS -------------------
        Route::get('/tags', [TagController::class, 'index'])
            ->name('tags.index')->middleware('can:tag.index');
        Route::get('/tags/create', [TagController::class, 'create'])
            ->name('tags.create')->middleware('can:tag.create');
        Route::post('/tags/store', [TagController::class, 'store'])
            ->name('tags.store')->middleware('can:tag.store');
        Route::get('/tags/{tag}', [TagController::class, 'show'])
            ->name('tags.show')->middleware('can:tag.show');
        Route::get('/tags/{tag}/edit', [TagController::class, 'edit'])
            ->name('tags.edit')->middleware('can:tag.edit');
        Route::put('/tags/{tag}', [TagController::class, 'update'])
            ->name('tags.update')->middleware('can:tag.update');
        Route::delete('/tags/{tag}', [TagController::class, 'destroy'])
            ->name('tags.destroy')->middleware('can:tag.destroy');

        // ------------------- ITENS (TECNOLOGIAS ASSISTIVAS E MATERIAIS PEDAGÓGICOS) -------------------
        Route::get('/items', [ItemController::class, 'index'])
            ->name('items.index')->middleware('can:item.index');

        Route::get('/items/create', [ItemController::class, 'create'])
            ->name('items.create')->middleware('can:item.create');

        Route::post('/items/store', [ItemController::class, 'store'])
            ->name('items.store')->middleware('can:item.store');

        Route::get('/items/{item}/clone', [ItemController::class, 'clone'])
            ->name('items.clone')->middleware('can:item.create');

        Route::get('/items/{item}/inspection/{inspection}', [ItemController::class, 'showInspection'])
            ->name('items.inspection.show')->middleware('can:item.inspection.show');

        Route::get('/items/{item}', [ItemController::class, 'show'])
            ->name('items.show')->middleware('can:item.show');

        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])
            ->name('items.edit')->middleware('can:item.edit');

        Route::put('/items/{item}', [ItemController::class, 'update'])
            ->name('items.update')->middleware('can:item.update');

        Route::delete('/items/{item}', [ItemController::class, 'destroy'])
            ->name('items.destroy')->middleware('can:item.destroy');

        Route::get('/items/{item}/pdf', [ItemController::class, 'generatePdf'])
            ->name('items.pdf')->middleware('can:item.pdf');

        // ------------------- BARREIRAS -------------------
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

        Route::delete('/barriers/{barrier}', [BarrierController::class, 'destroy'])
            ->name('barriers.destroy')->middleware('can:barrier.destroy');

        Route::get('/barriers/{barrier}/pdf', [BarrierController::class, 'generatePdf'])
            ->name('barriers.pdf')->middleware('can:barrier.pdf');

        // ------------------- PUBLICAÇÕES -------------------
        // Endereços da antiga agenda institucional, usados em links já enviados.
        Route::permanentRedirect('/institutional-events', '/inclusive-radar/publications');
        Route::get('/institutional-events/{path}', fn (string $path) => redirect("/inclusive-radar/publications/{$path}", 301))
            ->where('path', '.*');

        Route::get('/publications', [PublicationController::class, 'index'])
            ->name('publications.index')->middleware('can:publication.index');

        Route::get('/publications/create', [PublicationController::class, 'create'])
            ->name('publications.create')->middleware('can:publication.create');

        Route::post('/publications/store', [PublicationController::class, 'store'])
            ->name('publications.store')->middleware('can:publication.store');

        Route::get('/publications/{publication}', [PublicationController::class, 'show'])
            ->name('publications.show')->middleware('can:publication.show');

        Route::get('/publications/{publication}/edit', [PublicationController::class, 'edit'])
            ->name('publications.edit')->middleware('can:publication.edit');

        Route::put('/publications/{publication}', [PublicationController::class, 'update'])
            ->name('publications.update')->middleware('can:publication.update');

        Route::delete('/publications/{publication}', [PublicationController::class, 'destroy'])
            ->name('publications.destroy')->middleware('can:publication.destroy');

        Route::get('/publications/{publication}/pdf', [PublicationController::class, 'generatePdf'])
            ->name('publications.pdf')->middleware('can:publication.pdf');

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
});

Route::prefix('specialized-educational-support')->name('specialized-educational-support.')->group(function (): void {
    Route::middleware(['auth'])->group(function () {

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
        Route::get('/students/{student}/pdf', [StudentController::class, 'pdf'])->name('students.pdf')->middleware('can:student.view');

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
        Route::get('/teachers/permissions', [TeacherController::class, 'permissions'])->name('teachers.permissions')->middleware('can:teacher.update');
        Route::put('/teachers/permissions/update', [TeacherController::class, 'updatePermissions'])->name('teachers.permissions.update')->middleware('can:teacher.update');
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index')->middleware('can:teacher.view');
        Route::get('/teachers/{teacher}/show', [TeacherController::class, 'show'])->name('teachers.show')->middleware('can:teacher.view');
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create')->middleware('can:teacher.create');
        Route::post('/teachers/store', [TeacherController::class, 'store'])->name('teachers.store')->middleware('can:teacher.create');
        Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit')->middleware('can:teacher.update');
        Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update')->middleware('can:teacher.update');
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy')->middleware('can:teacher.delete');
        Route::get('/teachers/{teacher}/disciplines', [TeacherController::class, 'disciplines'])->name('teachers.disciplines')->middleware('can:teacher.update');
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
        Route::get('students/{student}/deficiencies', [StudentDeficiencyController::class, 'index'])->name('student-deficiencies.index')->middleware('can:student-deficiency.view');
        Route::get('students/{student}/deficiencies/create', [StudentDeficiencyController::class, 'create'])->name('student-deficiencies.create')->middleware('can:student-deficiency.create');
        Route::post('students/{student}/deficiencies', [StudentDeficiencyController::class, 'store'])->name('student-deficiencies.store')->middleware('can:student-deficiency.create');
        Route::get('students/{student}/deficiencies/{student_deficiency}', [StudentDeficiencyController::class, 'show'])->name('student-deficiencies.show')->middleware('can:student-deficiency.view');
        Route::get('students/{student}/deficiencies/{student_deficiency}/edit', [StudentDeficiencyController::class, 'edit'])->name('student-deficiencies.edit')->middleware('can:student-deficiency.update');
        Route::put('students/{student}/deficiencies/{student_deficiency}', [StudentDeficiencyController::class, 'update'])->name('student-deficiencies.update')->middleware('can:student-deficiency.update');
        Route::delete('students/{student}/deficiencies/{student_deficiency}', [StudentDeficiencyController::class, 'destroy'])->name('student-deficiencies.destroy')->middleware('can:student-deficiency.delete');

        /* 7. SESSIONS */
        Route::get('sessions', [SessionController::class, 'index'])->name('sessions.index')->middleware('can:session.view-all');
        Route::get('sessions/create', [SessionController::class, 'create'])->name('sessions.create')->middleware('can:session.create');
        Route::post('sessions/store', [SessionController::class, 'store'])->name('sessions.store')->middleware('can:session.create');
        Route::get('sessions/{session}/show', [SessionController::class, 'show'])->name('sessions.show')->middleware('can:session.view');
        Route::get('sessions/{session}/edit', [SessionController::class, 'edit'])->name('sessions.edit')->middleware('can:session.update');
        Route::put('sessions/{session}', [SessionController::class, 'update'])->name('sessions.update')->middleware('can:session.update');
        Route::delete('sessions/{session}', [SessionController::class, 'destroy'])->name('sessions.destroy')->middleware('can:session.delete');
        Route::post('sessions/{session}/restore', [SessionController::class, 'restore'])->withTrashed()->name('sessions.restore')->middleware('can:session.update');
        Route::delete('sessions/{session}/force-delete', [SessionController::class, 'forceDelete'])->withTrashed()->name('sessions.force-delete')->middleware('can:session.delete');
        Route::get('sessions/availability', [SessionController::class, 'availability'])->name('sessions.availability')->middleware('can:session.view');
        Route::post('sessions/{session}/cancel', [SessionController::class, 'cancel'])->name('sessions.cancel')->middleware('can:session.update');
        Route::get('students/{student}/sessions', [SessionController::class, 'indexByStudent'])->name('students.sessions.index')->middleware('can:session.view');
        Route::get('my-sessions', [SessionController::class, 'mySessions'])->name('sessions.my-sessions')->middleware('can:session.view-own');

        /* 8. AEE RECORDS */
        Route::get('aee-records', [AeeRecordController::class, 'index'])->name('aee-records.index')->middleware('can:aee-record.view-all');
        Route::get('my-aee-records', [AeeRecordController::class, 'myRecords'])->name('aee-records.my-records')->middleware('can:aee-record.view-own');
        Route::get('aee-records/{session}/create', [AeeRecordController::class, 'create'])->name('aee-records.create')->middleware('can:aee-record.create');
        Route::post('aee-records/store', [AeeRecordController::class, 'store'])->name('aee-records.store')->middleware('can:aee-record.create');
        Route::get('aee-records/{aeeRecord}/show', [AeeRecordController::class, 'show'])->name('aee-records.show')->middleware('can:aee-record.view');
        Route::get('aee-records/{aeeRecord}/edit', [AeeRecordController::class, 'edit'])->name('aee-records.edit')->middleware('can:aee-record.update');
        Route::put('aee-records/{aeeRecord}', [AeeRecordController::class, 'update'])->name('aee-records.update')->middleware('can:aee-record.update');
        Route::delete('aee-records/{aeeRecord}', [AeeRecordController::class, 'destroy'])->name('aee-records.destroy')->middleware('can:aee-record.delete');
        Route::get('aee-records/{aeeRecord}/pdf', [AeeRecordController::class, 'pdf'])->name('aee-records.pdf')->middleware('can:aee-record.view');
        Route::get('students/{student}/aee-records', [AeeRecordController::class, 'studentIndex'])->name('students.aee-records.index')->middleware('can:aee-record.view');
        Route::get('students/{student}/aee-records/pdf', [AeeRecordController::class, 'studentHistoryPdf'])->name('students.aee-records.history-pdf')->middleware('can:aee-record.view');
        Route::get('students/{student}/aee-records/{evaluation}/show', [AeeRecordController::class, 'studentShow'])->name('students.aee-records.show')->middleware('can:aee-record.view');
        Route::get('students/{student}/aee-records/{evaluation}/edit', [AeeRecordController::class, 'studentEdit'])->name('students.aee-records.edit')->middleware('can:aee-record.update');
        Route::put('students/{student}/aee-records/{evaluation}', [AeeRecordController::class, 'studentUpdate'])->name('students.aee-records.update')->middleware('can:aee-record.update');
        Route::delete('students/{student}/aee-records/{evaluation}', [AeeRecordController::class, 'studentDestroy'])->name('students.aee-records.destroy')->middleware('can:aee-record.delete');
        Route::get('students/{student}/aee-records/{evaluation}/pdf', [AeeRecordController::class, 'studentPdf'])->name('students.aee-records.pdf')->middleware('can:aee-record.view');

        /* 8.1 PEDAGOGICAL RECORDS */
        Route::get('pedagogical-records', [PedagogicalRecordController::class, 'index'])
            ->name('pedagogical-records.index')
            ->middleware('can:pedagogical-record.view-all');
        Route::get('my-pedagogical-records', [PedagogicalRecordController::class, 'myRecords'])
            ->name('pedagogical-records.my-records')
            ->middleware('can:pedagogical-record.view-own');
        Route::get('pedagogical-records/{session}/create', [PedagogicalRecordController::class, 'create'])
            ->name('pedagogical-records.create')
            ->middleware('can:pedagogical-record.create');
        Route::post('pedagogical-records/store', [PedagogicalRecordController::class, 'store'])
            ->name('pedagogical-records.store')
            ->middleware('can:pedagogical-record.create');
        Route::get('pedagogical-records/{pedagogicalRecord}/show', [PedagogicalRecordController::class, 'show'])
            ->name('pedagogical-records.show')
            ->middleware('can:pedagogical-record.view');
        Route::get('pedagogical-records/{pedagogicalRecord}/edit', [PedagogicalRecordController::class, 'edit'])
            ->name('pedagogical-records.edit')
            ->middleware('can:pedagogical-record.update');
        Route::put('pedagogical-records/{pedagogicalRecord}', [PedagogicalRecordController::class, 'update'])
            ->name('pedagogical-records.update')
            ->middleware('can:pedagogical-record.update');
        Route::delete('pedagogical-records/{pedagogicalRecord}', [PedagogicalRecordController::class, 'destroy'])
            ->name('pedagogical-records.destroy')
            ->middleware('can:pedagogical-record.delete');
        Route::get('pedagogical-records/{pedagogicalRecord}/pdf', [PedagogicalRecordController::class, 'pdf'])
            ->name('pedagogical-records.pdf')
            ->middleware('can:pedagogical-record.view');
        Route::get('students/{student}/pedagogical-records', [PedagogicalRecordController::class, 'studentIndex'])
            ->name('students.pedagogical-records.index')
            ->middleware('can:pedagogical-record.view');
        Route::get('students/{student}/pedagogical-records/pdf', [PedagogicalRecordController::class, 'studentHistoryPdf'])
            ->name('students.pedagogical-records.pdf')
            ->middleware('can:pedagogical-record.view');

        /* 9. STUDENT COURSES */
        Route::get('/student-courses/{student}/create', [StudentCourseController::class, 'create'])->name('student-courses.create')->middleware('can:student-course.create');
        Route::post('/student-courses/store/{student}', [StudentCourseController::class, 'store'])->name('student-courses.store')->middleware('can:student-course.create');
        Route::get('/students/{student}/history', [StudentCourseController::class, 'index'])->name('student-courses.history')->middleware('can:student-course.view');
        Route::get('student-courses/{studentCourse}', [StudentCourseController::class, 'show'])->name('student-courses.show')->middleware('can:student-course.view');
        Route::get('/student-courses/{studentCourse}/edit', [StudentCourseController::class, 'edit'])->name('student-courses.edit')->middleware('can:student-course.update');
        Route::put('/student-courses/{studentCourse}', [StudentCourseController::class, 'update'])->name('student-courses.update')->middleware('can:student-course.update');
        Route::patch('/student-courses/{studentCourse}/make-current', [StudentCourseController::class, 'makeCurrent'])->name('student-courses.make-current')->middleware('can:student-course.update');
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
        Route::post('/peis/student/{student}/store', [PeiController::class, 'store'])->name('pei.store')->middleware('can:pei.create');
        Route::delete('/peis/{pei}', [PeiController::class, 'destroy'])->name('pei.destroy')->middleware('can:pei.delete');
        Route::patch('/peis/{pei}/finish', [PeiController::class, 'finish'])->name('pei.finish')->middleware('can:pei.update');
        Route::post('peis/{student}/version', [PeiController::class, 'createVersion'])->name('pei.version.newVersion')->middleware('can:pei.create');
        Route::get('/peis/{pei}/disciplinas/{peiDiscipline}/pdf', [PeiController::class, 'generateDisciplinePdf'])->name('pei.discipline.pdf')->middleware('can:pei.view');
        Route::get('/peis/{pei}/pdf', [PeiController::class, 'generateCompletePdf'])->name('pei.pdf')->middleware('can:pei.view');

        Route::get('/peis/{pei}/disciplines/create', [PeiController::class, 'createDiscipline'])->name('pei-discipline.create')->middleware('can:pei-discipline.create');
        Route::post('/peis/{pei}/disciplines/store', [PeiController::class, 'storeDiscipline'])->name('pei-discipline.store')->middleware('can:pei-discipline.create');
        Route::get('/peis/{pei}/disciplines/{peiDiscipline}/show', [PeiController::class, 'showDiscipline'])->name('pei-discipline.show')->middleware('can:pei-discipline.view')->scopeBindings();
        Route::get('/peis/{pei}/disciplines/{peiDiscipline}/edit', [PeiController::class, 'editDiscipline'])->name('pei-discipline.edit')->middleware('can:pei-discipline.update')->scopeBindings();
        Route::put('/peis/{pei}/disciplines/{peiDiscipline}', [PeiController::class, 'updateDiscipline'])->name('pei-discipline.update')->middleware('can:pei-discipline.update')->scopeBindings();
        Route::delete('/peis/{pei}/disciplines/{peiDiscipline}', [PeiController::class, 'destroyDiscipline'])->name('pei-discipline.destroy')->middleware('can:pei-discipline.delete')->scopeBindings();
        Route::get('/peis/{pei}/teacher-disciplines', [PeiController::class, 'teacherDisciplines'])->name('teacher-disciplines');

        /* 13. STUDENT DOCUMENTS */
        Route::get('/students/{student}/documents', [StudentDocumentController::class, 'index'])->name('student-documents.index')->middleware('can:student-document.view');
        Route::get('/students/{student}/documents/create', [StudentDocumentController::class, 'create'])->name('student-documents.create')->middleware('can:student-document.create');
        Route::post('/students/{student}/documents', [StudentDocumentController::class, 'store'])->name('student-documents.store')->middleware('can:student-document.create');
        Route::get('/student-documents/{student_document}/edit', [StudentDocumentController::class, 'edit'])->name('student-documents.edit')->middleware('can:student-document.update');
        Route::put('/student-documents/{student_document}', [StudentDocumentController::class, 'update'])->name('student-documents.update')->middleware('can:student-document.update');
        Route::delete('/student-documents/{student_document}', [StudentDocumentController::class, 'destroy'])->name('student-documents.destroy')->middleware('can:student-document.delete');
        Route::get('/student-documents/{student_document}/download', [StudentDocumentController::class, 'download'])->name('student-documents.download')->middleware('can:student-document.view');
        Route::get('/student-documents/{student_document}/view', [StudentDocumentController::class, 'show'])->name('student-documents.view')->middleware('can:student-document.view');

    });
});
