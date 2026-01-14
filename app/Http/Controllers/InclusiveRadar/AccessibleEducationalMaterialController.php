<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AccessibleEducationalMaterialRequest;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\InclusiveRadar\AccessibleEducationalMaterialService;

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
            'inclusive-radar.accessible-educational-materials.index',
            compact('materials')
        );
    }

    public function create()
    {
        $deficiencies = Deficiency::where('is_active', true)->get();

        return view(
            'inclusive-radar.accessible-educational-materials.create',
            compact('deficiencies')
        );
    }

    public function store(AccessibleEducationalMaterialRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material pedagógico acessível criado com sucesso!');
    }

    public function edit(
        AccessibleEducationalMaterial $accessibleEducationalMaterial
    ) {
        $deficiencies = Deficiency::where('is_active', true)->get();

        return view(
            'inclusive-radar.accessible-educational-materials.edit',
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
            ->route('inclusive-radar.accessible-educational-materials.index')
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
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', $message);
    }

    public function destroy(
        AccessibleEducationalMaterial $accessibleEducationalMaterial
    ) {
        $this->service->delete($accessibleEducationalMaterial);

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material pedagógico acessível removido com sucesso!');
    }
}
