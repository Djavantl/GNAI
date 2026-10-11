<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses\CreateStudentCourseAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses\DeleteStudentCourseAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses\SetCurrentStudentCourseAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses\UpdateStudentCourseAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentCourses\CreateStudentCourseData;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentCourses\ListStudentCoursesData;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentCourses\UpdateStudentCourseData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentCourses\ListStudentCoursesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentCourses\ShowStudentCourseQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentCourses\StudentCourseFilterCoursesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentCourses\StudentCourseFormQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class StudentCourseController
{
    public function index(
        ListStudentCoursesData $filters,
        Student $student,
        ListStudentCoursesQuery $query,
        StudentCourseFilterCoursesQuery $filterCourses,
        Request $request,
    ): InertiaResponse {
        $studentCourses = $query->execute($student, $filters);

        return Inertia::render('SpecializedEducationalSupport/StudentCourses/Index', [
            'student' => $student->loadMissing('person'),
            'studentCourses' => $studentCourses,
            'courses' => $filterCourses->execute($student),
        ]);
    }

    public function show(StudentCourse $studentCourse, ShowStudentCourseQuery $query): InertiaResponse
    {
        $studentCourse = $query->execute($studentCourse);

        return Inertia::render(
            'SpecializedEducationalSupport/StudentCourses/Show',
            compact('studentCourse'),
        );
    }

    public function create(Student $student, StudentCourseFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/StudentCourses/Create',
            $form->forCreation($student),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(
        CreateStudentCourseData $data,
        Student $student,
        CreateStudentCourseAction $action,
    ): RedirectResponse {
        $action->execute($student, $data);

        return redirect()
            ->route('specialized-educational-support.student-courses.history', $student)
            ->with('success', 'Matrícula realizada com sucesso.');
    }

    public function edit(StudentCourse $studentCourse, StudentCourseFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/StudentCourses/Edit',
            $form->forUpdate($studentCourse),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(
        UpdateStudentCourseData $data,
        StudentCourse $studentCourse,
        UpdateStudentCourseAction $action,
    ): RedirectResponse {
        $studentCourse = $action->execute($studentCourse, $data);

        return redirect()
            ->route('specialized-educational-support.student-courses.show', $studentCourse)
            ->with('success', 'Dados da matrícula atualizados.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(StudentCourse $studentCourse, DeleteStudentCourseAction $action): RedirectResponse
    {
        $student = $studentCourse->student_id;

        $action->execute($studentCourse);

        return redirect()
            ->route('specialized-educational-support.student-courses.history', $student)
            ->with('success', 'Registro de histórico removido.');
    }

    /**
     * @throws Throwable
     */
    public function makeCurrent(
        StudentCourse $studentCourse,
        SetCurrentStudentCourseAction $action,
    ): RedirectResponse {
        $studentCourse = $action->execute($studentCourse);

        return redirect()
            ->route('specialized-educational-support.student-courses.show', $studentCourse)
            ->with('success', 'Curso definido como atual com sucesso.');
    }
}
