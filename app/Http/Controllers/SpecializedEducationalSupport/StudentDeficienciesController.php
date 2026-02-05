<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentDeficiencies;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\SpecializedEducationalSupport\StudentDeficienciesService;
use App\Http\Requests\SpecializedEducationalSupport\StudentDeficienciesRequest;

class StudentDeficienciesController extends Controller
{
    protected StudentDeficienciesService $service;

    public function __construct(StudentDeficienciesService $service)
    {
        $this->service = $service;
    }

    // Lista todas as deficiências de um aluno específico.

    public function index(Student $student)
    {
        $deficiencies = $this->service->index($student);
        return view('pages.specialized-educational-support.student-deficiencies.index', compact('student', 'deficiencies'));
    }

    //lista deficiencia especifica

    public function show(StudentDeficiencies $student_deficiency)
    {
        $deficiency = $this->service->show($student_deficiency);
        $student = $student_deficiency->student;
        return view('pages.specialized-educational-support.student-deficiencies.show', compact('deficiency', 'student'));
    }


    // Exibe o formulário de criação.

    public function create(Student $student)
    {
        $deficienciesList = Deficiency::orderBy('name')->get();
        return view('pages.specialized-educational-support.student-deficiencies.create', compact('student', 'deficienciesList'));
    }

    // Salva o vínculo da deficiência.

    public function store(Student $student, StudentDeficienciesRequest $request)
    {
        $this->service->create($student, $request->validated());

        return redirect()
            ->route('specialized-educational-support.student-deficiencies.index', $student)
            ->with('success', 'Deficiência vinculada com sucesso.');
    }

    // Exibe o formulário de edição.

    public function edit(StudentDeficiencies $student_deficiency)
    {
        // Obtém o aluno através do relacionamento (ou campo student_id)
        $student = $student_deficiency->student;

        $deficienciesList = Deficiency::orderBy('name')->get();

        return view('pages.specialized-educational-support.student-deficiencies.edit',
            compact('student', 'student_deficiency', 'deficienciesList')
        );
    }

    // Atualiza os dados da deficiência vinculada.

    public function update(StudentDeficiencies $student_deficiency, StudentDeficienciesRequest $request)
    {
        $this->service->update($student_deficiency, $request->validated());
        $student = $student_deficiency->student;

        return redirect()
            ->route('specialized-educational-support.student-deficiencies.index', $student)
            ->with('success', 'Informações da deficiência atualizadas.');
    }

    // Remove o vínculo.

    public function destroy(StudentDeficiencies $student_deficiency)
    {
        $student = $student_deficiency->student_id;
        $this->service->delete($student_deficiency);

        return redirect()
            ->route('specialized-educational-support.student-deficiencies.index', $student)
            ->with('success', 'Vínculo removido com sucesso.');
    }
}
