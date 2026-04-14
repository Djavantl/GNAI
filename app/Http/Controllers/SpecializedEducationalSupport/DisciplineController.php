<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Discipline;
use App\Models\SpecializedEducationalSupport\Course;
use App\Http\Requests\SpecializedEducationalSupport\DisciplineRequest;
use App\Services\SpecializedEducationalSupport\DisciplineService;
use Illuminate\Http\Request;
use Exception;

class DisciplineController extends Controller
{
    protected DisciplineService $service;

    public function __construct(DisciplineService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $disciplines = $this->service->index($request->all());
            $courses = Course::orderBy('name')->pluck('name', 'id')->toArray();

            if ($request->ajax()) {
                return view(
                    'pages.specialized-educational-support.disciplines.partials.table',
                    compact('disciplines')
                )->render();
            }

            return view(
                'pages.specialized-educational-support.disciplines.index',
                compact('disciplines', 'courses')
            );
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar disciplinas: ' . $e->getMessage());
        }
    }

    public function show(Discipline $discipline)
    {
        try {
            $discipline->load('courses');

            return view(
                'pages.specialized-educational-support.disciplines.show',
                compact('discipline')
            );
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao exibir disciplina: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('pages.specialized-educational-support.disciplines.create');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao abrir formulário de criação: ' . $e->getMessage());
        }
    }

    public function store(DisciplineRequest $request)
    {
        try {
            $this->service->create($request->validated());

            return redirect()
                ->route('specialized-educational-support.disciplines.index')
                ->with('success', 'Disciplina cadastrada com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao salvar disciplina: ' . $e->getMessage());
        }
    }

    public function edit(Discipline $discipline)
    {
        try {
            return view('pages.specialized-educational-support.disciplines.edit', compact('discipline'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao abrir formulário de edição: ' . $e->getMessage());
        }
    }

    public function update(DisciplineRequest $request, Discipline $discipline)
    {
        try {
            $this->service->update($discipline, $request->validated());

            return redirect()
                ->route('specialized-educational-support.disciplines.index')
                ->with('success', 'Disciplina atualizada com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar disciplina: ' . $e->getMessage());
        }
    }

    public function destroy(Discipline $discipline)
    {
        try {
            $this->service->delete($discipline);

            return redirect()
                ->route('specialized-educational-support.disciplines.index')
                ->with('success', 'Disciplina removida com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao remover disciplina: ' . $e->getMessage());
        }
    }
}