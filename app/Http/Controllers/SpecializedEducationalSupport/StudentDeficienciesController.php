<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentDeficiencies;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\SpecializedEducationalSupport\StudentDeficienciesService;
use App\Http\Requests\SpecializedEducationalSupport\StudentDeficienciesRequest;
use Illuminate\Http\Request;
use Throwable;

class StudentDeficienciesController extends Controller
{
    protected StudentDeficienciesService $service;

    public function __construct(StudentDeficienciesService $service)
    {
        $this->service = $service;
    }

    // Lista todas as deficiências de um aluno específico.
    public function index(Request $request, Student $student)
    {
        try {
            // 1. Busca as deficiências vinculadas ao aluno com os filtros aplicados
            $deficiencies = $this->service->index($student, $request->all());

            // 2. Resposta AJAX para o filtro dinâmico
            if ($request->ajax()) {
                return view(
                    'pages.specialized-educational-support.student-deficiencies.partials.table', 
                    compact('student', 'deficiencies')
                )->render();
            }

            // 3. Pegar apenas as deficiências que este aluno já possui
            $filterDeficiencies = Deficiency::whereHas('students', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

            return view(
                'pages.specialized-educational-support.student-deficiencies.index',
                compact('student', 'deficiencies', 'filterDeficiencies')
            );

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao listar deficiências do aluno.');
        }
    }

    // Mostra uma deficiência específica vinculada ao aluno
    public function show(Student $student, StudentDeficiencies $student_deficiency)
    {
        abort_if($student_deficiency->student_id !== $student->id, 404);

        try {
            $deficiency = $this->service->show($student_deficiency);

            return view(
                'pages.specialized-educational-support.student-deficiencies.show',
                compact('deficiency', 'student')
            );

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao exibir deficiência do aluno.');
        }
    }

    // Exibe o formulário de criação
    public function create(Student $student)
    {
        $student->ensureIsActive();

        $deficienciesList = Deficiency::orderBy('name')->get();

        return view(
            'pages.specialized-educational-support.student-deficiencies.create',
            compact('student', 'deficienciesList')
        );
    }

    // Salva o vínculo da deficiência
    public function store(Student $student, StudentDeficienciesRequest $request)
    {
        try {
            $student->ensureIsActive();

            $this->service->create($student, $request->validated());

            return redirect()
                ->route('specialized-educational-support.student-deficiencies.index', $student)
                ->with('success', 'Deficiência vinculada com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao vincular deficiência.');
        }
    }

    // Exibe o formulário de edição (somente contexto)
    public function edit(Student $student, StudentDeficiencies $student_deficiency)
    {
        abort_if($student_deficiency->student_id !== $student->id, 404);

        $student->ensureIsActive();

        return view(
            'pages.specialized-educational-support.student-deficiencies.edit',
            compact('student', 'student_deficiency')
        );
    }

    // Atualiza somente o contexto da deficiência
    public function update(
        Student $student,
        StudentDeficiencies $student_deficiency,
        StudentDeficienciesRequest $request
    ){
        abort_if($student_deficiency->student_id !== $student->id, 404);

        try {
            $student->ensureIsActive();

            $this->service->update($student_deficiency, $request->validated());

            return redirect()
                ->route('specialized-educational-support.student-deficiencies.index', $student)
                ->with('success', 'Informações da deficiência atualizadas.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao atualizar deficiência.');
        }
    }

    // Remove o vínculo
    public function destroy(Student $student, StudentDeficiencies $student_deficiency)
    {
        abort_if($student_deficiency->student_id !== $student->id, 404);

        try {
            $this->service->delete($student_deficiency);

            return redirect()
                ->route('specialized-educational-support.student-deficiencies.index', $student)
                ->with('success', 'Vínculo removido com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao remover vínculo da deficiência.');
        }
    }
}