<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AccessibleEducationalMaterialRequest;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Services\InclusiveRadar\AccessibleEducationalMaterialService;
use App\Services\InclusiveRadar\ResourceAttributeValueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccessibleEducationalMaterialController extends Controller
{
    protected AccessibleEducationalMaterialService $service;
    protected ResourceAttributeValueService $attributeService;

    public function __construct(AccessibleEducationalMaterialService $service, ResourceAttributeValueService $attributeService)
    {
        $this->service = $service;
        $this->attributeService = $attributeService;
    }

    public function index(): View
    {
        $materials = $this->service->listAll();

        return view(
            'inclusive-radar.accessible-educational-materials.index',
            compact('materials')
        );
    }

    public function create(): View
    {
        $data = $this->service->getCreateData();

        return view(
            'inclusive-radar.accessible-educational-materials.create',
            $data
        );
    }

    public function store(AccessibleEducationalMaterialRequest $request): RedirectResponse
    {
        $material = $this->service->store($request->validated());

        $this->attributeService->saveValues(
            'educational_material',
            $material->id,
            $request->input('attributes', [])
        );

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material pedagógico acessível criado com sucesso!');
    }

    public function edit(AccessibleEducationalMaterial $material): View
    {
        $data = $this->service->getEditData($material);

        return view(
            'inclusive-radar.accessible-educational-materials.edit',
            $data
        );
    }

    public function update(AccessibleEducationalMaterialRequest $request, AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->update($material, $request->validated());

        $this->attributeService->saveValues(
            'educational_material',
            $material->id,
            $request->input('attributes', [])
        );

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material pedagógico acessível atualizado com sucesso!');
    }

    public function toggleActive(AccessibleEducationalMaterial $material): RedirectResponse
    {
        $updatedMaterial = $this->service->toggleActive($material);

        $message = $updatedMaterial->is_active
            ? 'Material pedagógico acessível ativado com sucesso!'
            : 'Material pedagógico acessível desativado com sucesso!';

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', $message);
    }

    public function destroy(AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->delete($material);

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material pedagógico acessível removido com sucesso!');
    }

}
