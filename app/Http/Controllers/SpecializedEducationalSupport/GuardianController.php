<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Guardian;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use Illuminate\Http\Request;
use App\Http\Requests\SpecializedEducationalSupport\GuardianRequest;
use App\Services\SpecializedEducationalSupport\GuardianService;
use Exception;

class GuardianController extends Controller
{
    protected GuardianService $service;

    public function __construct(GuardianService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request, Student $student)
    {
        try {
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
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao listar responsáveis: ' . $e->getMessage());
        }
    }

    public function show(Guardian $guardian)
    {
        try {
            $guardian = $this->service->show($guardian);
            $student = $guardian->student;
            return view('pages.specialized-educational-support.guardians.show', compact('guardian', 'student'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao exibir responsável: ' . $e->getMessage());
        }
    }

    public function create(Student $student)
    {
        try {
            $student->ensureIsActive();
            return view('pages.specialized-educational-support.guardians.create', compact('student'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function store(GuardianRequest $request, Student $student)
    {
        try {
            $student->ensureIsActive();
            $guardian = $this->service->create($student, $request->validated());

            return redirect()
                ->route('specialized-educational-support.guardians.show', $guardian)
                ->with('success', 'Responsável vinculado com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(Student $student, Guardian $guardian)
    {
        try {
            if ($guardian->student_id !== $student->id) {
                abort(404);
            }
            $student->ensureIsActive();
            $guardian->load('person');
            return view('pages.specialized-educational-support.guardians.edit', compact('student', 'guardian'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(GuardianRequest $request, Student $student, Guardian $guardian)
    {
        try {
            $student->ensureIsActive();
            $guardian = $this->service->update($guardian, $request->validated());

            return redirect()
                ->route('specialized-educational-support.guardians.show', $guardian)
                ->with('success', 'Dados do responsável atualizados com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    
    public function destroy(string $student, string $guardian)
    {
        try {
            $guardian = Guardian::where('id', $guardian)
                ->where('student_id', $student)
                ->firstOrFail();

            $this->service->delete($guardian);

            return redirect()
                ->route('specialized-educational-support.guardians.index', $student)
                ->with('success', 'Responsável removido.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao remover responsável: ' . $e->getMessage());
        }
    }
}