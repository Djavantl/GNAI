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
        $studentCourses = $this->service->getHistoryByStudent($student->id);
        return view('pages.specialized-educational-support.student-courses.index', compact('student', 'studentCourses'));
    }

    public function show(StudentCourse $studentCourse)
    {
        $studentCourse->load([
            'student.person',
            'course.disciplines',
            'logs.user'
        ]);

        return view(
            'pages.specialized-educational-support.student-courses.show',
            compact('studentCourse')
        );
    }
    public function create(Student $student)
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get();
    
        return view('pages.specialized-educational-support.student-courses.create', compact('student', 'courses'));
    }

    public function store(Student $student, StudentCourseRequest $request)
    {
        $this->service->enroll($student, $request->validated());

        return redirect()
            ->route('specialized-educational-support.student-courses.history', $student)
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
            ->route('specialized-educational-support.student-courses.show', $studentCourse)
            ->with('success', 'Dados da matrícula atualizados.');
    }

    public function destroy(StudentCourse $studentCourse)
    {
        $student = $studentCourse->student_id;
        $this->service->deleteEnrollment($studentCourse);

        return redirect()
            ->route('specialized-educational-support.student-courses.history', $student)
            ->with('success', 'Registro de histórico removido.');
    }
}
