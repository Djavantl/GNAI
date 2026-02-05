<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecializedEducationalSupport\StudentRequest;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use App\Services\SpecializedEducationalSupport\StudentService;

class StudentController extends Controller
{
    protected StudentService $service;

    public function __construct(StudentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $students = $this->service->index();
        return view('pages.specialized-educational-support.students.index', compact('students'));
    }

    public function show(Student $student)
    {
        $student = $this->service->show($student);
        return view('pages.specialized-educational-support.students.show', compact('student'));
    }

    public function create()
    {
        $people = Person::orderBy('name')->get();
        return view('pages.specialized-educational-support.students.create', compact('people'));
    }

    public function store(StudentRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('specialized-educational-support.students.index')
            ->with('success', 'Aluno cadastrado com sucesso.');
    }

    public function edit(Student $student)
    {
        $people = Person::orderBy('name')->get();
        return view('pages.specialized-educational-support.students.edit', compact('student', 'people'));
    }

    public function update(StudentRequest $request, Student $student)
    {
        $this->service->update($student, $request->validated());

        return redirect()
            ->route('specialized-educational-support.students.index')
            ->with('success', 'Aluno atualizado com sucesso.');
    }

    public function destroy(Student $student)
    {
        $this->service->delete($student);

        return redirect()
            ->route('specialized-educational-support.students.index')
            ->with('success', 'Aluno removido com sucesso.');
    }
}
