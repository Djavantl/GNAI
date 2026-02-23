<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Discipline;
use App\Models\SpecializedEducationalSupport\Course;
use App\Http\Requests\SpecializedEducationalSupport\DisciplineRequest;
use App\Services\SpecializedEducationalSupport\DisciplineService;
use Illuminate\Http\Request;


class DisciplineController extends Controller
{
    protected DisciplineService $service;

    public function __construct(DisciplineService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $disciplines = $this->service->index($request->all());
        
        $courses = Course::orderBy('name')->pluck('name', 'id')->toArray();

        if ($request->ajax()) {
            return view('pages.specialized-educational-support.disciplines.partials.table', compact('disciplines'))->render();
        }

        return view('pages.specialized-educational-support.disciplines.index', compact('disciplines', 'courses'));
    }

    public function show(Discipline $discipline)
    {
        return view('pages.specialized-educational-support.disciplines.show', compact('discipline'));
    }

    public function create()
    {
        return view('pages.specialized-educational-support.disciplines.create');
    }

    public function store(DisciplineRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('specialized-educational-support.disciplines.index')
            ->with('success', 'Disciplina cadastrada com sucesso.');
    }

    public function edit(Discipline $discipline)
    {
        return view('pages.specialized-educational-support.disciplines.edit', compact('discipline'));
    }

    public function update(DisciplineRequest $request, Discipline $discipline)
    {
        $this->service->update($discipline, $request->validated());

        return redirect()
            ->route('specialized-educational-support.disciplines.index')
            ->with('success', 'Disciplina atualizada com sucesso.');
    }

    public function destroy(Discipline $discipline)
    {
        $this->service->delete($discipline);

        return redirect()
            ->route('specialized-educational-support.disciplines.index')
            ->with('success', 'Disciplina removida com sucesso.');
    }
}
