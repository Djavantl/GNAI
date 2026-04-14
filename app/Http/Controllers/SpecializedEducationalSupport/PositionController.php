<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Http\Requests\SpecializedEducationalSupport\PositionRequest;
use App\Models\SpecializedEducationalSupport\Position;
use App\Services\SpecializedEducationalSupport\PositionService;
use Illuminate\Http\Request;

class PositionController extends Controller
{

    protected PositionService $service;

    public function __construct(PositionService $service){
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $positions = $this->service->index($request->all());

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.positions.partials.table',
                compact('positions')
            )->render();
        }

        return view(
            'pages.specialized-educational-support.positions.index',
            compact('positions')
        );
    }

    public function show(Position $position)
    {      
        $position = $position->load('permissions');
        return view('pages.specialized-educational-support.positions.show', compact('position'));
    }

    public function create()
    {
        $permissions = $this->getGroupedPermissions();
        return view('pages.specialized-educational-support.positions.create', compact('permissions'));
    }

    public function store(PositionRequest $request)
    {
        try {
            $this->service->store($request->validated());

            return redirect()
                ->route('specialized-educational-support.positions.index')
                ->with('success', 'Cargo criado com sucesso!');
        } catch (\DomainException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Erro inesperado ao criar o cargo.');
        }
    }

    
    public function edit(Position $position)
    {
        $permissions = $this->getGroupedPermissions();
        $selectedPermissions = $position->permissions->pluck('id')->toArray();

        return view('pages.specialized-educational-support.positions.edit', 
            compact('position', 'permissions', 'selectedPermissions')
        );
    }

    public function update(PositionRequest $request, Position $position)
    {
        try {
            $this->service->update($position, $request->validated());

            return redirect()
                ->route('specialized-educational-support.positions.index')
                ->with('success', 'Cargo atualizado com sucesso!');
        } catch (\DomainException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Erro inesperado ao atualizar o cargo.');
        }
    }

    public function toggleActive(Position $position)
    {
        try {
            $this->service->toggleActive($position);

            $message = $position->is_active
                ? 'Cargo ativado com sucesso!'
                : 'Cargo desativado com sucesso!';

            return redirect()
                ->route('specialized-educational-support.positions.index')
                ->with('success', $message);

        } catch (\DomainException $e) {
            return redirect()
                ->route('specialized-educational-support.positions.index')
                ->with('error', $e->getMessage());

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('specialized-educational-support.positions.index')
                ->with('error', 'Erro inesperado ao alterar o status do cargo.');
        }
    }

    public function destroy(Position $position)
    {
        try {
            $this->service->delete($position);

            return redirect()
                ->route('specialized-educational-support.positions.index')
                ->with('success', 'Cargo removido!');
        } catch (\DomainException $e) {
            return redirect()
                ->route('specialized-educational-support.positions.index')
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('specialized-educational-support.positions.index')
                ->with('error', 'Erro inesperado ao remover o cargo.');
        }
    }

    private function getGroupedPermissions()
    {
        return Permission::all()->groupBy(function ($permission) {
    
            $prefix = explode('.', $permission->slug)[0];

            return __("permissions.entities.{$prefix}") !== "permissions.entities.{$prefix}" 
                ? __("permissions.entities.{$prefix}") 
                : ucfirst($prefix);
        });
    }
}
