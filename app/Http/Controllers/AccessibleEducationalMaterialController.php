<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccessibleEducationalMaterialRequest;
use App\Models\AccessibleEducationalMaterial;
use App\Models\Deficiency;
use App\Services\AccessibleEducationalMaterialService;

class AccessibleEducationalMaterialController extends Controller
{
    protected AccessibleEducationalMaterialService $service;

    public function __construct(AccessibleEducationalMaterialService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $materials = $this->service->listAll();

        return view(
            'accessible-educational-materials.index',
            compact('materials')
        );
    }

    public function create()
    {
        $deficiencies = Deficiency::where('is_active', true)->get();

        return view(
            'accessible-educational-materials.create',
            compact('deficiencies')
        );
    }

    public function store(AccessibleEducationalMaterialRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('accessible-educational-materials.index')
            ->with('success', 'Material pedagógico acessível criado com sucesso!');
    }

    public function edit(
        AccessibleEducationalMaterial $accessibleEducationalMaterial
    ) {
        $deficiencies = Deficiency::where('is_active', true)->get();

        return view(
            'accessible-educational-materials.edit',
            compact('accessibleEducationalMaterial', 'deficiencies')
        );
    }

    public function update(
        AccessibleEducationalMaterialRequest $request,
        AccessibleEducationalMaterial $accessibleEducationalMaterial
    ) {
        $this->service->update(
            $accessibleEducationalMaterial,
            $request->validated()
        );

        return redirect()
            ->route('accessible-educational-materials.index')
            ->with('success', 'Material pedagógico acessível atualizado com sucesso!');
    }

    public function toggleActive(
        AccessibleEducationalMaterial $accessibleEducationalMaterial
    ) {
        $material = $this->service->toggleActive($accessibleEducationalMaterial);

        $message = $material->is_active
            ? 'Material pedagógico acessível ativado com sucesso!'
            : 'Material pedagógico acessível desativado com sucesso!';

        return redirect()
            ->route('accessible-educational-materials.index')
            ->with('success', $message);
    }

    public function destroy(
        AccessibleEducationalMaterial $accessibleEducationalMaterial
    ) {
        $this->service->delete($accessibleEducationalMaterial);

        return redirect()
            ->route('accessible-educational-materials.index')
            ->with('success', 'Material pedagógico acessível removido com sucesso!');
    }
}
