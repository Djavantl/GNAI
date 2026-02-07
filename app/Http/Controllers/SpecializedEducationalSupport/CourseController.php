<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Course;
use App\Http\Requests\SpecializedEducationalSupport\CourseRequest;
use App\Models\SpecializedEducationalSupport\Discipline;
use App\Services\SpecializedEducationalSupport\CourseService;


class CourseController extends Controller
{
    protected CourseService $service;

    public function __construct(CourseService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $courses = $this->service->index();
        return view('pages.specialized-educational-support.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $course = $this->service->show($course);
        return view('pages.specialized-educational-support.courses.show', compact('course'));
    }

    public function create()
    {
        $disciplines = Discipline::where('is_active', true)->orderBy('name')->get();
        return view('pages.specialized-educational-support.courses.create', compact('disciplines'));
    }

    public function store(CourseRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('specialized-educational-support.courses.index')
            ->with('success', 'Curso cadastrado com sucesso.');
    }

    public function edit(Course $course)
    {
        $disciplines = Discipline::where('is_active', true)->orderBy('name')->get();
        $course->load('disciplines');
        return view('pages.specialized-educational-support.courses.edit', compact('course', 'disciplines'));
    }

    public function update(CourseRequest $request, Course $course)
    {
        $this->service->update($course, $request->validated());

        return redirect()
            ->route('specialized-educational-support.courses.index')
            ->with('success', 'Curso atualizado com sucesso.');
    }

    public function destroy(Course $course)
    {
        $this->service->delete($course);

        return redirect()
            ->route('specialized-educational-support.courses.index')
            ->with('success', 'Curso removido com sucesso.');
    }
}
