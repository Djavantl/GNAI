<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Guardian;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use Illuminate\Http\Request;
use App\Http\Requests\SpecializedEducationalSupport\GuardianRequest;
use App\Services\SpecializedEducationalSupport\GuardianService;


class GuardianController extends Controller
{
    protected GuardianService $service;

    public function __construct(GuardianService $service)
    {
        $this->service = $service;
    }

    public function index(Student $student)
    {
        $guardians = $this->service->listByStudent($student->id);
        return view('specialized-educational-support.guardians.index', compact('student', 'guardians'));
    }

    public function create(Student $student)
    {
        return view('specialized-educational-support.guardians.create', compact('student'));
    }

    public function store(GuardianRequest $request, Student $student)
    {
        $this->service->create($student, $request->validated());

        return redirect()
            ->route('specialized-educational-support.students.index')
            ->with('success', 'Responsável vinculado com sucesso.');
    }

     public function edit(Guardian $guardian)
    {
        $people = Person::orderBy('name')->get();
        return view('specialized-educational-support.students.edit', compact('student', 'people'));
    }

    public function update(GuardianRequest $request, Guardian $guardian)
    {
        $this->service->update($guardian, $request->validated());

        return redirect()
            ->route('specialized-educational-support.guardians.index')
            ->with('success', 'Aluno atualizado com sucesso.');
    }

    public function destroy(string $student, string $guardian)
    {
        $guardian = Guardian::where('id', $guardian)
            ->where('student_id', $student)
            ->firstOrFail();

        $this->service->delete($guardian);

        return redirect()
            ->route('specialized-educational-support.guardians.index', $student)
            ->with('success', 'Responsável removido.');
    }
}