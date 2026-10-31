<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Presenters;

use App\Domains\Inclusion\Domain\Models\Inspection;
use App\Domains\Inclusion\Domain\Models\InspectionEvidence;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ItemPresenter
{
    /**
     * @param  LengthAwarePaginator<int, Item>  $items
     * @return LengthAwarePaginator<int, Item>
     */
    public function items(LengthAwarePaginator $items): LengthAwarePaginator
    {
        $items->getCollection()->each(static function (Item $item): void {
            $item->setAttribute('conservation_state_label', $item->conservation_state?->label());
            $item->setAttribute('resource_status_label', $item->status?->label());
        });

        return $items;
    }

    /**
     * @return array<string, mixed>
     */
    public function display(Item $item): array
    {
        return [
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
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function inspection(Inspection $inspection): array
    {
        return [
            'date' => $inspection->inspection_date?->format('d/m/Y') ?? '---',
            'stateLabel' => $inspection->state?->label() ?? '---',
            'typeLabel' => $inspection->type?->label() ?? '---',
            'descriptionHtml' => RichTextSanitizer::sanitize(
                (string) ($inspection->description ?: 'Nenhum parecer técnico registrado.'),
            ),
            'evidences' => $inspection->evidences->map(
                static fn (InspectionEvidence $evidence): array => [
                    'id' => $evidence->id,
                    'name' => $evidence->displayName(),
                    'url' => '/storage/'.ltrim((string) $evidence->path, '/'),
                    'isImage' => $evidence->isImage(),
                    'iconClass' => $evidence->iconClass(),
                ],
            )->all(),
        ];
    }
}
