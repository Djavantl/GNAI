<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Presenters;

use App\Domains\Inclusion\Domain\Models\Barrier;
use App\Domains\Inclusion\Domain\Models\Inspection;
use App\Shared\Infrastructure\Security\RichTextSanitizer;

final class BarrierShowPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function present(Barrier $barrier): array
    {
        return [
            'descriptionHtml' => RichTextSanitizer::sanitize($barrier->description ?: 'Sem descrição detalhada.'),
            'locationDetailsHtml' => $barrier->location_specific_details
                ? RichTextSanitizer::sanitize($barrier->location_specific_details)
                : null,
            'inspectionDetails' => $barrier->inspections->map(fn (Inspection $inspection): array => [
                'id' => $inspection->id,
                'date' => $inspection->inspection_date?->format('d/m/Y') ?? '---',
                'type' => $inspection->type?->label() ?? '---',
                'state' => $inspection->status?->label() ?? '---',
                'descriptionHtml' => $inspection->description
                    ? RichTextSanitizer::sanitize((string) $inspection->description)
                    : null,
                'evidenceCount' => $inspection->evidences->count(),
            ])->values()->all(),
        ];
    }
}
