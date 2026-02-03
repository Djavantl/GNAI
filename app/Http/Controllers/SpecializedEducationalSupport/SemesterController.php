<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Semester;
use App\Services\SpecializedEducationalSupport\SemesterService;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    protected SemesterService $service;

    public function __construct(SemesterService $service)
    {
        $this->service = $service;
    }

    /**
     * Listar semestres
     */
    public function index()
    {
        $semesters = $this->service->all();

        return view('pages.specialized-educational-support.semesters.index', compact('semesters'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        return view('pages.specialized-educational-support.semesters.create');
    }

    /**
     * Salvar semestre
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'year'       => ['required', 'integer'],
            'term'       => ['required', 'integer', 'min:1'],
            'label'      => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date'],
            'is_current' => ['boolean'],
        ]);

        $this->service->create($data);

        return redirect()
            ->route('specialized-educational-support.semesters.index')
            ->with('success', 'Semestre criado com sucesso.');
    }

    /**
     * Formulário de edição
     */
    public function edit(Semester $semester)
    {
        return view('pages.specialized-educational-support.semesters.edit', compact('semester'));
    }

    /**
     * Atualizar semestre
     */
    public function update(Request $request, Semester $semester)
    {
        $data = $request->validate([
            'year'       => ['required', 'integer'],
            'term'       => ['required', 'integer', 'min:1'],
            'label'      => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date'],
            'is_current' => ['boolean'],
        ]);

        $this->service->update($semester, $data);

        return redirect()
            ->route('specialized-educational-support.semesters.index')
            ->with('success', 'Semestre atualizado com sucesso.');
    }

    /**
     * Definir semestre como atual
     */
    public function setCurrent(Semester $semester)
    {
        $this->service->setCurrent($semester);

        return redirect()
            ->route('specialized-educational-support.semesters.index')
            ->with('success', 'Semestre definido como atual.');
    }

    /**
     * Remover semestre
     */
    public function destroy(Semester $semester)
    {
        $this->service->delete($semester);

        return redirect()
            ->route('specialized-educational-support.semesters.index')
            ->with('success', 'Semestre removido com sucesso.');
    }
}
