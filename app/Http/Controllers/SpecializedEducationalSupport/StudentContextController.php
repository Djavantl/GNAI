<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SpecializedEducationalSupport\StudentContextRequest;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentContext;
use App\Services\SpecializedEducationalSupport\StudentContextService;

class StudentContextController extends Controller
{
    protected StudentContextService $service;

    public function __construct(StudentContextService $service)
    {
        $this->service = $service;
    }

    public function show(Student $student)
    {
        $context = $this->service->show($student);
        return view('specialized-educational-support.student-context.show', compact('student', 'context'));
    }

    public function create(Student $student)
    {
        return view('specialized-educational-support.student-context.create', compact('student'));
    }

    public function store(Student $student, StudentContextRequest $request)
    {
        $this->service->create($student, $request->validated());

        return redirect()
            ->route('specialized-educational-support.student-context.show', $student)
            ->with('success', 'Contexto do aluno cadastrado com sucesso.');
    }

    public function edit(StudentContext $student_context)
    {
        return view('specialized-educational-support.student-context.edit', compact('student_context'));
    }

    public function update(StudentContext $student_context, StudentContextRequest $request)
    {
        $student = $student_context->student;
        $this->service->update($student_context, $request->validated());

        return redirect()
            ->route('specialized-educational-support.student-context.show', $student)
            ->with('success', 'Contexto do aluno atualizado com sucesso.');
    }

    public function destroy(StudentContext $student_context)
    {   
        $student = $student_context->student;
        $this->service->delete($student_context);

        return redirect()
            ->route('specialized-educational-support.student-context.show', $student)
            ->with('success', 'Contexto do aluno removido com sucesso.');
    }
}
