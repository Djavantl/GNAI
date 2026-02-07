<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\Student;
use App\Http\Requests\SpecializedEducationalSupport\StudentCourseRequest;
use App\Models\SpecializedEducationalSupport\StudentCourse;
use App\Services\SpecializedEducationalSupport\StudentCourseService;

class StudentCourseController extends Controller
{
    protected StudentCourseService $service;

    public function __construct(StudentCourseService $service)
    {
        $this->service = $service;
    }

    public function index(Student $student)
    {
        $history = $this->service->getHistoryByStudent($student->id);
        return view('pages.specialized-educational-support.student-courses.index', compact('student', 'history'));
    }

    public function create()
    {
        $students = Student::with('person')->get();
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        return view('pages.specialized-educational-support.student-courses.create', compact('students', 'courses'));
    }

    public function store(StudentCourseRequest $request)
    {
        $this->service->enroll($request->validated());

        return redirect()
            ->route('specialized-educational-support.students.index')
            ->with('success', 'Matrícula realizada com sucesso.');
    }

    public function edit(StudentCourse $studentCourse)
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        return view('pages.specialized-educational-support.student-courses.edit', compact('studentCourse', 'courses'));
    }

    public function update(StudentCourseRequest $request, StudentCourse $studentCourse)
    {
        $this->service->updateEnrollment($studentCourse, $request->validated());

        return redirect()
            ->back()
            ->with('success', 'Dados da matrícula atualizados.');
    }

    public function destroy(StudentCourse $studentCourse)
    {
        $this->service->deleteEnrollment($studentCourse);

        return redirect()
            ->back()
            ->with('success', 'Registro de histórico removido.');
    }
}
