<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\Permission;
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
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->slug)[0];
        });

        return view('pages.specialized-educational-support.positions.create', compact('permissions'));
    }

    public function store(PositionRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()->route('specialized-educational-support.positions.index')->with('success', 'Cargo criado com sucesso!');
    }

    
    public function edit(Position $position)
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->slug)[0];
        });

        $selectedPermissions = $position->permissions->pluck('id')->toArray();

        return view('pages.specialized-educational-support.positions.edit',
            compact('position', 'permissions', 'selectedPermissions')
        );
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
