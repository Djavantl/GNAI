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
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\Models\InspectionEvidence;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class AccessibleEducationalMaterialController
{
    public function index(ListAccessibleEducationalMaterialsData $filters, ListAccessibleEducationalMaterialsQuery $query, Request $request): InertiaResponse
    {
        $materials = $query->execute($filters);

        return Inertia::render(
            'InclusiveRadar/AccessibleEducationalMaterials/Index',
            [
                'materials' => $materials,
                'accessibilityFeatureOptions' => $this->accessibilityFeatureOptions(),
                'filters' => $request->only([
                    'name',
                    'is_digital',
                    'accessibility_feature_id',
                    'is_active',
                    'available',
                    'per_page',
                ]),
                'can' => [
                    'create' => $request->user()?->can('material.create') ?? false,
                    'show' => $request->user()?->can('material.show') ?? false,
                    'destroy' => $request->user()?->can('material.destroy') ?? false,
                ],
            ],
        );
    }

    public function create(AccessibleEducationalMaterialFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/AccessibleEducationalMaterials/Create',
            $form->forCreation() + [
                'can' => ['submit' => $request->user()?->can('material.store') ?? false],
            ],
        );
    }

    public function clone(AccessibleEducationalMaterial $material, AccessibleEducationalMaterialFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/AccessibleEducationalMaterials/Create',
            $form->forCloning($material) + [
                'can' => ['submit' => $request->user()?->can('material.store') ?? false],
            ],
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

    public function show(AccessibleEducationalMaterial $material, ShowAccessibleEducationalMaterialQuery $query, Request $request): InertiaResponse
    {
        $material = $query->execute($material);

        return Inertia::render('InclusiveRadar/AccessibleEducationalMaterials/Show', [
            'material' => $material,
            'deficiencies' => $material->deficiencies,
            'features' => $material->accessibilityFeatures,
            'display' => [
                'notesHtml' => RichTextSanitizer::sanitize((string) ($material->notes ?: '---')),
                'statusLabel' => $material->status?->label() ?? '---',
                'inspectionDetails' => $material->inspections->map(fn ($inspection) => [
                    'id' => $inspection->id,
                    'date' => $inspection->inspection_date?->format('d/m/Y') ?? '---',
                    'type' => $inspection->type?->label() ?? '---',
                    'state' => $inspection->state?->label() ?? '---',
                    'descriptionHtml' => $inspection->description
                        ? RichTextSanitizer::sanitize((string) $inspection->description)
                        : null,
                    'evidenceCount' => $inspection->evidences->count(),
                ])->all(),
            ],
            'can' => [
                'clone' => $request->user()?->can('material.create') ?? false,
                'edit' => $request->user()?->can('material.edit') ?? false,
                'inspection' => $request->user()?->can('material.inspection.show') ?? false,
                'pdf' => $request->user()?->can('material.pdf') ?? false,
                'destroy' => $request->user()?->can('material.destroy') ?? false,
            ],
        ]);
    }

    public function edit(AccessibleEducationalMaterial $material, AccessibleEducationalMaterialFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/AccessibleEducationalMaterials/Edit',
            $form->forUpdate($material) + [
                'can' => ['submit' => $request->user()?->can('material.update') ?? false],
            ],
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
            ->setPaper('a4', 'portrait');

        PdfPageNumberer::apply($pdf);

        return $pdf->stream("MPA_{$material->name}.pdf");
    }

    public function showInspection(AccessibleEducationalMaterial $material, Inspection $inspection, ShowAccessibleEducationalMaterialInspectionQuery $query): InertiaResponse
    {
        $scopedInspection = $query->execute(
            material: $material,
            inspection: $inspection,
        );

        return Inertia::render('InclusiveRadar/AccessibleEducationalMaterials/Inspections/Show', [
            'material' => $material->only(['id', 'name']),
            'inspection' => [
                'date' => $scopedInspection->inspection_date?->format('d/m/Y') ?? '---',
                'stateLabel' => $scopedInspection->state?->label() ?? '---',
                'typeLabel' => $scopedInspection->type?->label() ?? '---',
                'descriptionHtml' => RichTextSanitizer::sanitize(
                    (string) ($scopedInspection->description ?: 'Nenhum parecer técnico registrado.'),
                ),
                'evidences' => $scopedInspection->evidences->map(
                    static fn (InspectionEvidence $evidence): array => [
                        'id' => $evidence->id,
                        'name' => $evidence->displayName(),
                        'url' => '/storage/'.ltrim((string) $evidence->path, '/'),
                        'isImage' => $evidence->isImage(),
                        'iconClass' => $evidence->iconClass(),
                    ],
                )->all(),
            ],
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function accessibilityFeatureOptions(): array
    {
        return ['' => 'Recurso de acessibilidade (Todos)']
            + AccessibilityFeature::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->mapWithKeys(
                    static fn (string $name, int $id): array => [(string) $id => $name],
                )
                ->all();
    }
}
