<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\UI\Controllers;

use App\Domains\InclusiveRadar\Application\Actions\AccessibleEducationalMaterials\CreateAccessibleEducationalMaterialAction;
use App\Domains\InclusiveRadar\Application\Actions\AccessibleEducationalMaterials\DeleteAccessibleEducationalMaterialAction;
use App\Domains\InclusiveRadar\Application\Actions\AccessibleEducationalMaterials\UpdateAccessibleEducationalMaterialAction;
use App\Domains\InclusiveRadar\Application\Data\AccessibleEducationalMaterials\CreateAccessibleEducationalMaterialData;
use App\Domains\InclusiveRadar\Application\Data\AccessibleEducationalMaterials\ListAccessibleEducationalMaterialsData;
use App\Domains\InclusiveRadar\Application\Data\AccessibleEducationalMaterials\UpdateAccessibleEducationalMaterialData;
use App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials\AccessibleEducationalMaterialFormQuery;
use App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials\AccessibleEducationalMaterialPdfQuery;
use App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials\ListAccessibleEducationalMaterialsQuery;
use App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials\ShowAccessibleEducationalMaterialInspectionQuery;
use App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials\ShowAccessibleEducationalMaterialQuery;
use App\Domains\InclusiveRadar\Domain\Exceptions\AssetCodeAlreadyInUse;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

final class AccessibleEducationalMaterialController extends Controller
{
    public function index(ListAccessibleEducationalMaterialsData $filters,ListAccessibleEducationalMaterialsQuery $query,Request $request): View
    {
        $materials = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.accessible-educational-materials.partials.table',
                compact('materials'),
            );
        }

        return view(
            'pages.inclusive-radar.accessible-educational-materials.index',
            compact('materials'),
        );
    }

    public function create(AccessibleEducationalMaterialFormQuery $form): View
    {
        return view(
            'pages.inclusive-radar.accessible-educational-materials.create',
            $form->forCreation(),
        );
    }

    public function store(CreateAccessibleEducationalMaterialData $data, CreateAccessibleEducationalMaterialAction $action, Request $request): RedirectResponse
    {
        try {
            $action->execute(
                data: $data,
                registeredBy: (int) $request->user()?->getAuthIdentifier(),
            );
        } catch (AssetCodeAlreadyInUse $exception) {
            return back()
                ->withInput()
                ->withErrors(['asset_code' => $exception->getMessage()]);
        }

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material criado com sucesso!');
    }

    public function show(AccessibleEducationalMaterial $material, ShowAccessibleEducationalMaterialQuery $query): View
    {
        $material = $query->execute($material);

        return view('pages.inclusive-radar.accessible-educational-materials.show', [
            'material' => $material,
            'deficiencies' => $material->deficiencies,
            'features' => $material->accessibilityFeatures,
            'inspections' => $material->inspections,
        ]);
    }

    public function edit(AccessibleEducationalMaterial $material, AccessibleEducationalMaterialFormQuery $form): View
    {
        return view(
            'pages.inclusive-radar.accessible-educational-materials.edit',
            $form->forUpdate($material),
        );
    }

    public function update(UpdateAccessibleEducationalMaterialData $data, AccessibleEducationalMaterial $material, UpdateAccessibleEducationalMaterialAction $action, Request $request): RedirectResponse
    {
        try {
            $action->execute(
                material: $material,
                data: $data,
                registeredBy: (int) $request->user()?->getAuthIdentifier(),
            );
        } catch (AssetCodeAlreadyInUse $exception) {
            return back()
                ->withInput()
                ->withErrors(['asset_code' => $exception->getMessage()]);
        }

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material atualizado com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function destroy(AccessibleEducationalMaterial $material, DeleteAccessibleEducationalMaterialAction $action): RedirectResponse
    {
        $action->execute($material);

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material removido!');
    }

    public function generatePdf(AccessibleEducationalMaterial $material, AccessibleEducationalMaterialPdfQuery $query): Response
    {
        $material = $query->execute($material);
        $pdf = Pdf::loadView(
            'pages.inclusive-radar.accessible-educational-materials.pdf',
            compact('material'),
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

        return $pdf->stream("MPA_{$material->name}.pdf");
    }

    public function showInspection(AccessibleEducationalMaterial $material, Inspection $inspection, ShowAccessibleEducationalMaterialInspectionQuery $query): View
    {
        $scopedInspection = $query->execute(
            material: $material,
            inspection: $inspection,
        );

        return view('pages.inclusive-radar.accessible-educational-materials.inspections.show', [
            'material' => $material,
            'inspection' => $scopedInspection,
        ]);
    }
}
