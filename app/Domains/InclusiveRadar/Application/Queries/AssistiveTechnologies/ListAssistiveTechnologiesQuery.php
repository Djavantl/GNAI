<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Application\Data\AssistiveTechnologies\ListAssistiveTechnologiesData;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListAssistiveTechnologiesQuery
{
    /**
     * @return LengthAwarePaginator<int, AssistiveTechnology>
     */
    public function execute(
        ListAssistiveTechnologiesData $filters,
    ): LengthAwarePaginator {
        $query = AssistiveTechnology::query();

        if (filled($filters->name)) {
            $query->where(
                'name',
                'like',
                '%'.trim((string) $filters->name).'%',
            );
        }

        if ($filters->isDigital !== null) {
            $query->where('is_digital', $filters->isDigital);
        }

        if ($filters->isActive !== null) {
            $query->where('is_active', $filters->isActive);
        }

        if ($filters->available === true) {
            $query->where('quantity_available', '>', 0);
        }

        if ($filters->available === false) {
            $query->where('quantity_available', '<=', 0);
        }

        return $query
            ->orderBy('name')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
