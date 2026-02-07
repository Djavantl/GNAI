<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AccessibleEducationalMaterialRequest;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Services\InclusiveRadar\AccessibleEducationalMaterialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccessibleEducationalMaterialController extends Controller
{
    public function __construct(
        protected AccessibleEducationalMaterialService $service
    ) {}

    public function index(): View
    {
        $materials = AccessibleEducationalMaterial::with([
            'type',
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures'
        ])
            ->orderBy('name')
            ->get();

        return view('pages.inclusive-radar.accessible-educational-materials.index', compact('materials'));
    }

    public function create(): View
    {
        // A view create recebe $deficiencies e $resourceTypes via View Composer
        return view('pages.inclusive-radar.accessible-educational-materials.create');
    }

    public function store(AccessibleEducationalMaterialRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material criado com sucesso!');
    }

    public function show(AccessibleEducationalMaterial $material): View
    {
        $material->load([
            'type',
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures',
            'inspections.images',
            'loans',
            'attributeValues.attribute'
        ]);

        $attributeValues = $material->attributeValues
            ->pluck('value', 'attribute_id')
            ->toArray();

        return view(
            'pages.inclusive-radar.accessible-educational-materials.show',
            compact('material', 'attributeValues')
        );
    }

    public function edit(AccessibleEducationalMaterial $material): View
    {
        $material->load([
            'deficiencies',
            'accessibilityFeatures',
            'inspections.images',
            'attributeValues.attribute'
        ]);

        $attributeValues = $material->attributeValues
            ->pluck('value', 'attribute_id')
            ->toArray();

        // A view de edição recebe $deficiencies e $resourceTypes via View Composer
        return view(
            'pages.inclusive-radar.accessible-educational-materials.edit',
            compact('material', 'attributeValues')
        );
    }

    public function update(AccessibleEducationalMaterialRequest $request, AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->update($material, $request->validated());

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material atualizado com sucesso!');
    }

    public function toggleActive(AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->toggleActive($material);

        return redirect()->back()
            ->with('success', 'Status atualizado!');
    }

    public function destroy(AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->delete($material);

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material removido!');
    }
}
