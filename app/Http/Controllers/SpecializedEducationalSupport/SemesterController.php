<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Semester;
use App\Services\SpecializedEducationalSupport\SemesterService;
use Illuminate\Http\Request;
use Exception;

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
    public function index(Request $request)
    {
        try {
            $semesters = $this->service->index($request->all());

            if ($request->ajax()) {
                return view(
                    'pages.specialized-educational-support.semesters.partials.table',
                    compact('semesters')
                )->render();
            }

            return view(
                'pages.specialized-educational-support.semesters.index',
                compact('semesters')
            );
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao listar semestres: ' . $e->getMessage());
        }
    }

    public function show(Semester $semester)
    {
        try {
            return view('pages.specialized-educational-support.semesters.show', compact('semester'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao exibir semestre: ' . $e->getMessage());
        }
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        try {
            return view('pages.specialized-educational-support.semesters.create');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar formulário: ' . $e->getMessage());
        }
    }

    /**
     * Salvar semestre
     */
    public function store(Request $request)
    {
        try {
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
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao criar semestre: ' . $e->getMessage());
        }
    }

    /**
     * Formulário de edição
     */
    public function edit(Semester $semester)
    {
        try {
            return view('pages.specialized-educational-support.semesters.edit', compact('semester'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar edição: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar semestre
     */
    public function update(Request $request, Semester $semester)
    {
        try {
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
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar semestre: ' . $e->getMessage());
        }
    }

    /**
     * Definir semestre como atual
     */
    public function setCurrent(Semester $semester)
    {
        try {
            $this->service->setCurrent($semester);

            return redirect()
                ->route('specialized-educational-support.semesters.index')
                ->with('success', 'Semestre definido como atual.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remover semestre
     */
    public function destroy(Semester $semester)
    {
        try {
            $this->service->delete($semester);

            return redirect()
                ->route('specialized-educational-support.semesters.index')
                ->with('success', 'Semestre removido com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao remover semestre: ' . $e->getMessage());
        }
    }
}