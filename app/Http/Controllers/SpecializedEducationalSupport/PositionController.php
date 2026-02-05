<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecializedEducationalSupport\PositionRequest;
use App\Models\SpecializedEducationalSupport\Position;
use App\Services\SpecializedEducationalSupport\PositionService;

class PositionController extends Controller
{

    protected PositionService $service;

    public function __construct(PositionService $service){
        $this->service = $service;
    }

    public function index()
    {
        $position = $this->service->index();

        return view('pages.specialized-educational-support.positions.index', compact('position'));
    }

    public function show(Position $position)
    {
        return view('pages.specialized-educational-support.positions.show', compact('position'));
    }

    public function create()
    {
        return view('pages.specialized-educational-support.positions.create');
    }

    public function store(PositionRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()->route('specialized-educational-support.positions.index')->with('success', 'Cargo criado com sucesso!');
    }

    public function edit(Position $position)
    {
        return view('pages.specialized-educational-support.positions.edit',compact('position'));
    }

    public function update(PositionRequest $request, Position $position)
    {
        $this->service->update($position, $request ->validated());

        return redirect()->route('specialized-educational-support.positions.index')->with('success', 'Cargo atualizado com sucesso!');
    }

    public function toggleActive(Position $Position)
    {
        $this->service->toggleActive($Position);

        if ($Position->is_active) {
            return redirect()->route('specialized-educational-support.positions.index')->with('success', 'Cargo ativado com sucesso!');
        } else {
           return redirect()->route('specialized-educational-support.positions.index')->with('success', 'Cargo desativado com sucesso!');
        }

    }

    public function destroy(Position $position)
    {
        $this->service->delete($position);

        return redirect()->route('specialized-educational-support.positions.index')->with('success', 'Cargo removido!');
    }
}
