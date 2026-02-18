<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AccessibleEducationalMaterialRequest;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Services\InclusiveRadar\AccessibleEducationalMaterialService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccessibleEducationalMaterialController extends Controller
{
    public function __construct(
        protected AccessibleEducationalMaterialService $service
    ) {}

    public function index(Request $request): View
    {
        $name = trim($request->name ?? '');

        $materials = AccessibleEducationalMaterial::with([
            'type',
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures',
        ])
            ->withCount('trainings')
            ->filterName($name ?: null)
            ->active($request->is_active)
            ->byType($request->type)
            ->digital($request->is_digital)
            ->available($request->available)
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.accessible-educational-materials.partials.table',
                compact('materials')
            );
        }

        return view(
            'pages.inclusive-radar.accessible-educational-materials.index',
            compact('materials')
        );
    }

    public function create(): View
    {
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
            'attributeValues.attribute',
            'trainings',
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
            'attributeValues.attribute',
            'trainings',
        ]);

        $attributeValues = $material->attributeValues
            ->pluck('value', 'attribute_id')
            ->toArray();

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

        return redirect()->back()->with('success', 'Status atualizado com sucesso!');
    }

    public function destroy(AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->delete($material);

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material removido!');
    }

    public function generatePdf(AccessibleEducationalMaterial $material)
    {
        $material->load([
            'type',
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures',
            'attributeValues.attribute',
            'inspections.images',
            'trainings',
        ]);

        $attributeValues = $material->attributeValues
            ->pluck('value', 'attribute_id')
            ->toArray();

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.accessible-educational-materials.pdf',
            compact('material', 'attributeValues')
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

        return $pdf->stream("MPA_{$material->name}.pdf");
    }
}
