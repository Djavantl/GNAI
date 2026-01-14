<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Person;
use App\Services\StudentService;
use App\Http\Requests\StudentRequest;

class StudentController extends Controller
{
    protected StudentService $service;

    public function __construct(StudentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $students = $this->service->all();
        return view('students.index', compact('students'));
    }

    public function create()
    {
        $people = Person::orderBy('name')->get();
        return view('students.create', compact('people'));
    }

    public function store(StudentRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('students.index')
            ->with('success', 'Aluno cadastrado com sucesso.');
    }

    public function edit(Student $student)
    {
        $people = Person::orderBy('name')->get();
        return view('students.edit', compact('student', 'people'));
    }

    public function update(StudentRequest $request, Student $student)
    {
        $this->service->update($student, $request->validated());

        return redirect()
            ->route('students.index')
            ->with('success', 'Aluno atualizado com sucesso.');
    }

    public function destroy(Student $student)
    {
        $this->service->delete($student);

        return redirect()
            ->route('students.index')
            ->with('success', 'Aluno removido com sucesso.');
    }
}
