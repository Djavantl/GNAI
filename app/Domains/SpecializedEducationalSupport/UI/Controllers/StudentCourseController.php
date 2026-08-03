<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses\CreateStudentCourseAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses\DeleteStudentCourseAction;
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
use Illuminate\View\View;
use Throwable;

final class StudentCourseController
{
    public function index(
        ListStudentCoursesData $filters,
        Student $student,
        ListStudentCoursesQuery $query,
        StudentCourseFilterCoursesQuery $filterCourses,
        Request $request,
    ): View {
        $studentCourses = $query->execute($student, $filters);

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.student-courses.partials.table',
                compact('student', 'studentCourses'),
            );
        }

        return view('pages.specialized-educational-support.student-courses.index', [
            'student' => $student->loadMissing('person'),
            'studentCourses' => $studentCourses,
            'courses' => $filterCourses->execute($student),
        ]);
    }

    public function show(StudentCourse $studentCourse, ShowStudentCourseQuery $query): View
    {
        $studentCourse = $query->execute($studentCourse);

        return view(
            'pages.specialized-educational-support.student-courses.show',
            compact('studentCourse'),
        );
    }

    public function create(Student $student, StudentCourseFormQuery $form): View
    {
        return view(
            'pages.specialized-educational-support.student-courses.create',
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

    public function edit(StudentCourse $studentCourse, StudentCourseFormQuery $form): View
    {
        return view(
            'pages.specialized-educational-support.student-courses.edit',
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
}
