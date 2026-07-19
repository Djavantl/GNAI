<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;

final class ShowAssistiveTechnologyQuery
{
    public function execute(
        AssistiveTechnology $technology,
    ): AssistiveTechnology {
        return $technology->load([
            'deficiencies' => static fn ($query) => $query->orderBy('name'),
            'inspections' => static fn ($query) => $query
                ->with('images')
                ->orderByDesc('inspection_date')
                ->orderByDesc('created_at'),
            'loans',
        ]);
    }
}
