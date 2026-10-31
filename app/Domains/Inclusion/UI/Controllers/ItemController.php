<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Items\CreateItemAction;
use App\Domains\Inclusion\Application\Actions\Items\DeleteItemAction;
use App\Domains\Inclusion\Application\Actions\Items\UpdateItemAction;
use App\Domains\Inclusion\Application\Data\Items\CreateItemData;
use App\Domains\Inclusion\Application\Data\Items\ListItemsData;
use App\Domains\Inclusion\Application\Data\Items\UpdateItemData;
use App\Domains\Inclusion\Application\Queries\Items\ItemFormQuery;
use App\Domains\Inclusion\Application\Queries\Items\ItemPdfQuery;
use App\Domains\Inclusion\Application\Queries\Items\ListItemsQuery;
use App\Domains\Inclusion\Application\Queries\Items\ShowItemInspectionQuery;
use App\Domains\Inclusion\Application\Queries\Items\ShowItemQuery;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Exceptions\AssetCodeAlreadyInUse;
use App\Domains\Inclusion\Domain\Models\AccessibilityFeature;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Inspection;
use App\Domains\Inclusion\Domain\Models\InspectionEvidence;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class ItemController
{
    public function index(ListItemsData $filters, ListItemsQuery $query, Request $request): InertiaResponse
    {
        $items = $query->execute($filters);
        $items->getCollection()->each(static function (Item $item): void {
            $item->setAttribute('conservation_state_label', $item->conservation_state?->label());
            $item->setAttribute('resource_status_label', $item->status?->label());
        });

        return Inertia::render(
            'Inclusion/Items/Index',
            [
                'items' => $items,
                'accessibilityFeatureOptions' => $this->accessibilityFeatureOptions(),
                'itemTypeOptions' => ['' => 'Tipo do item (Todos)'] + ItemType::options(),
                'filters' => $request->only([
                    'name',
                    'item_type',
                    'is_digital',
                    'accessibility_feature_id',
                    'is_active',
                    'available',
                    'per_page',
                ]),
                'can' => [
                    'create' => $request->user()?->can('item.create') ?? false,
                    'show' => $request->user()?->can('item.show') ?? false,
                    'destroy' => $request->user()?->can('item.destroy') ?? false,
                ],
            ],
        );
    }

    public function create(ItemFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Items/Create',
            $form->forCreation() + [
                'can' => ['submit' => $request->user()?->can('item.store') ?? false],
            ],
        );
    }

    public function clone(Item $item, ItemFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Items/Create',
            $form->forCloning($item) + [
                'can' => ['submit' => $request->user()?->can('item.store') ?? false],
            ],
        );
    }

    public function store(CreateItemData $data, CreateItemAction $action, Request $request): RedirectResponse
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
            ->route('inclusive-radar.items.index')
            ->with('success', 'Item criado com sucesso!');
    }

    public function show(Item $item, ShowItemQuery $query, Request $request): InertiaResponse
    {
        $item = $query->execute($item);

        return Inertia::render('Inclusion/Items/Show', [
            'item' => $item,
            'deficiencies' => $item->deficiencies,
            'features' => $item->accessibilityFeatures,
            'display' => [
                'itemTypeLabel' => $item->item_type?->label() ?? '---',
                'notesHtml' => RichTextSanitizer::sanitize((string) ($item->notes ?: '---')),
                'statusLabel' => $item->status?->label() ?? '---',
                'inspectionDetails' => $item->inspections->map(fn ($inspection) => [
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
                'clone' => $request->user()?->can('item.create') ?? false,
                'edit' => $request->user()?->can('item.edit') ?? false,
                'inspection' => $request->user()?->can('item.inspection.show') ?? false,
                'pdf' => $request->user()?->can('item.pdf') ?? false,
                'destroy' => $request->user()?->can('item.destroy') ?? false,
            ],
        ]);
    }

    public function edit(Item $item, ItemFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Items/Edit',
            $form->forUpdate($item) + [
                'can' => ['submit' => $request->user()?->can('item.update') ?? false],
            ],
        );
    }

    public function update(UpdateItemData $data, Item $item, UpdateItemAction $action, Request $request): RedirectResponse
    {
        try {
            $action->execute(
                item: $item,
                data: $data,
                registeredBy: (int) $request->user()?->getAuthIdentifier(),
            );
        } catch (AssetCodeAlreadyInUse $exception) {
            return back()
                ->withInput()
                ->withErrors(['asset_code' => $exception->getMessage()]);
        }

        return redirect()
            ->route('inclusive-radar.items.index')
            ->with('success', 'Item atualizado com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Item $item, DeleteItemAction $action): RedirectResponse
    {
        $action->execute($item);

        return redirect()
            ->route('inclusive-radar.items.index')
            ->with('success', 'Item removido com sucesso!');
    }

    public function generatePdf(Item $item, ItemPdfQuery $query): Response
    {
        $item = $query->execute($item);
        $pdf = Pdf::loadView(
            'pages.inclusion.items.pdf',
            compact('item'),
        )
            ->setPaper('a4', 'portrait');

        PdfPageNumberer::apply($pdf);

        $prefix = $item->item_type === ItemType::AssistiveTechnology ? 'TA' : 'MPA';

        return $pdf->stream("{$prefix}_{$item->name}.pdf");
    }

    public function showInspection(Item $item, Inspection $inspection, ShowItemInspectionQuery $query): InertiaResponse
    {
        $scopedInspection = $query->execute(
            item: $item,
            inspection: $inspection,
        );

        return Inertia::render('Inclusion/Items/Inspections/Show', [
            'item' => $item->only(['id', 'name']),
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
