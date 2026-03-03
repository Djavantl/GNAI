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

    public function index(Request $request, Student $student)
    {
        $guardians = $this->service->getByStudent($student, $request->all());

        if ($request->ajax()) {
            return view('pages.specialized-educational-support.guardians.partials.table', 
                compact('student', 'guardians')
            )->render();
        }

        // Pega os parentescos já cadastrados para este aluno para popular o select
        $relationships = Guardian::where('student_id', $student->id)
            ->distinct()
            ->pluck('relationship', 'relationship')
            ->toArray();

        return view('pages.specialized-educational-support.guardians.index', [
            'student' => $student,
            'guardians' => $guardians,
            'relationships' => $relationships
        ]);
    }

    public function show(Guardian $guardian)
    {
        $guardian = $this->service->show($guardian);
        $student = $guardian->student;
        return view('pages.specialized-educational-support.guardians.show', compact('guardian', 'student'));
    }

    public function create(Student $student)
    {
        return view('pages.specialized-educational-support.guardians.create', compact('student'));
    }

    public function store(GuardianRequest $request, Student $student)
    {
        $guardian = $this->service->create($student, $request->validated());

        return redirect()
            ->route('specialized-educational-support.guardians.show', $guardian)
            ->with('success', 'Responsável vinculado com sucesso.');
    }

    public function edit(Student $student, Guardian $guardian)
    {
        if ($guardian->student_id !== $student->id) {
            abort(404);
        }
        $guardian->load('person');
        return view('pages.specialized-educational-support.guardians.edit', compact('student', 'guardian'));
    }

    public function update(GuardianRequest $request, Student $student, Guardian $guardian)
    {
        $guardian = $this->service->update($guardian, $request->validated());

        return redirect()
            ->route('specialized-educational-support.guardians.show', $guardian)
            ->with('success', 'Dados do responsável atualizados com sucesso.');
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
