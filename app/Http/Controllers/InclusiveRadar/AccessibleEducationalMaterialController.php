<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AccessibleEducationalMaterialRequest;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\Inspection;
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

    /*
    |--------------------------------------------------------------------------
    | LISTAGEM
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $name = trim($request->name ?? '');

        $materials = AccessibleEducationalMaterial::with([
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures',
        ])
            ->withCount('trainings')
            ->filterName($name ?: null)
            ->active($request->is_active)
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

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(AccessibleEducationalMaterial $material): View
    {
        $material->load([
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures',
            'inspections.images',
            'loans',
            'trainings',
        ]);

        return view(
            'pages.inclusive-radar.accessible-educational-materials.show',
            compact('material')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(AccessibleEducationalMaterial $material): View
    {
        $material->load([
            'deficiencies',
            'accessibilityFeatures',
            'inspections.images',
            'trainings',
        ]);

        return view(
            'pages.inclusive-radar.accessible-educational-materials.edit',
            compact('material')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        AccessibleEducationalMaterialRequest $request,
        AccessibleEducationalMaterial $material
    ): RedirectResponse {
        $this->service->update($material, $request->validated());

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material atualizado com sucesso!');
    }

    /*
    |--------------------------------------------------------------------------
    | ATIVAR / DESATIVAR
    |--------------------------------------------------------------------------
    */

    public function toggleActive(AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->toggleActive($material);

        return redirect()->back()
            ->with('success', 'Status atualizado com sucesso!');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->delete($material);

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material removido!');
    }

    /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

    public function generatePdf(AccessibleEducationalMaterial $material)
    {
        $material->load([
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures',
            'inspections.images',
            'trainings',
        ]);

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.accessible-educational-materials.pdf',
            compact('material')
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

        return $pdf->stream("MPA_{$material->name}.pdf");
    }

    /*
    |--------------------------------------------------------------------------
    | INSPEÇÃO
    |--------------------------------------------------------------------------
    */

    public function showInspection(
        AccessibleEducationalMaterial $material,
        Inspection $inspection
    ) {
        abort_if(
            $inspection->inspectable_id !== $material->id ||
            $inspection->inspectable_type !== $material->getMorphClass(),
            403
        );

        $inspection->load('images', 'inspectable');

        return view(
            'pages.inclusive-radar.accessible-educational-materials.inspections.show',
            compact('material', 'inspection')
        );
    }
}
