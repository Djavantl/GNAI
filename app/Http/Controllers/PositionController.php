<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use App\Services\PositionService;
use App\Http\Requests\PositionRequest;

class PositionController extends Controller
{

    protected PositionService $service;

    public function __construct(PositionService $service){
        $this->service = $service;
    }

    public function index()
    {
        $position = $this->service->listAll();

        return view('positions.index', compact('position'));
    }

    public function create()
    {
        return view('positions.create');
    }

    public function store(PositionRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()->route('positions.index')->with('success', 'Cargo criado com sucesso!');
    }

    public function show(Position $position)
    {
        //
    }

    public function edit(Position $position)
    {
        return view('positions.edit',compact('position'));
    }

    public function update(PositionRequest $request, Position $position)
    {
        $this->service->update($position, $request ->validated());

        return redirect()->route('positions.index')->with('success', 'Cargo atualizado com sucesso!');
    }

    public function toggleActive(Position $Position)
    {
        $this->service->toggleActive($Position);

        if ($Position->is_active) {
            return redirect()->route('positions.index')->with('success', 'Cargo ativado com sucesso!');
        } else {
           return redirect()->route('positions.index')->with('success', 'Cargo desativado com sucesso!');
        }
        
    }

    public function destroy(Position $position)
    {
        $this->service->delete($position);

        return redirect()->route('positions.index')->with('success', 'Cargo removido!');
    }
}
