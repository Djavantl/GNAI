<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecializedEducationalSupport\DeficiencyRequest;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\SpecializedEducationalSupport\DeficiencyService;

class DeficiencyController extends Controller
{

    protected DeficiencyService $service;

    public function __construct(DeficiencyService $service){
        $this->service = $service;
    }

    public function index()
    {
        $deficiency = $this->service->index();

        return view('pages.specialized-educational-support.deficiencies.index', compact('deficiency'));
    }

     public function show(Deficiency $deficiency)
    {
        return view('pages.specialized-educational-support.deficiencies.show', compact('deficiency'));
    }

    public function create()
    {
        return view('pages.specialized-educational-support.deficiencies.create');
    }

    public function store(DeficiencyRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()->route('specialized-educational-support.deficiencies.index')->with('success', 'Deficiência criada com sucesso!');
    }

    public function edit(Deficiency $deficiency)
    {
        return view('pages.specialized-educational-support.deficiencies.edit',compact('deficiency'));
    }

    public function update(DeficiencyRequest $request, Deficiency $deficiency)
    {
        $this->service->update($deficiency, $request ->validated());

        return redirect()->route('specialized-educational-support.deficiencies.index')->with('success', 'Deficiência atualizada com sucesso!');
    }

    public function toggleActive(Deficiency $deficiency)
    {
        $this->service->toggleActive($deficiency);

        if ($deficiency->is_active) {
            return redirect()->route('specialized-educational-support.deficiencies.index')->with('success', 'Deficiência ativada com sucesso!');
        } else {
           return redirect()->route('specialized-educational-support.deficiencies.index')->with('success', 'Deficiência desativada com sucesso!');
        }

    }

    public function destroy(Deficiency $deficiency)
    {
        $this->service->delete($deficiency);

        return redirect()->route('specialized-educational-support.deficiencies.index')->with('success', 'Deficiência removida!');
    }
}
