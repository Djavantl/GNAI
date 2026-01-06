<?php

namespace App\Http\Controllers;

use App\Models\Deficiency;
use Illuminate\Http\Request;
use App\Services\DeficiencyService;
use App\Http\Requests\DeficiencyRequest;

class DeficiencyController extends Controller
{

    protected DeficiencyService $service;

    public function __construct(DeficiencyService $service){
        $this->service = $service;
    }

    public function index()
    {
        $deficiency = $this->service->listAll();

        return view('deficiencies.index', compact('deficiency'));
    }

    public function create()
    {
        return view('deficiencies.create');
    }

    public function store(DeficiencyRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()->route('deficiencies.index')->with('success', 'Deficiência criada com sucesso!');
    }

    public function show(Deficiency $deficiency)
    {
        //
    }

    public function edit(Deficiency $deficiency)
    {
        return view('deficiencies.edit',compact('deficiency'));
    }

    public function update(DeficiencyRequest $request, Deficiency $deficiency)
    {
        $this->service->update($deficiency, $request ->validated());

        return redirect()->route('deficiencies.index')->with('success', 'Deficiência atualizada com sucesso!');
    }

    public function toggleActive(Deficiency $deficiency)
    {
        $this->service->toggleActive($deficiency);

        if ($deficiency->is_active) {
            return redirect()->route('deficiencies.index')->with('success', 'Deficiência ativada com sucesso!');
        } else {
           return redirect()->route('deficiencies.index')->with('success', 'Deficiência desativada com sucesso!');
        }
        
    }

    public function destroy(Deficiency $deficiency)
    {
        $this->service->delete($deficiency);

        return redirect()->route('deficiencies.index')->with('success', 'Deficiência removida!');
    }
}
