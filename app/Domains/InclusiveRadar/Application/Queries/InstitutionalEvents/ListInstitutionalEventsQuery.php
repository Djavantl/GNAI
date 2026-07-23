<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\InstitutionalEvents;

use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\ListInstitutionalEventsData;
use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListInstitutionalEventsQuery
{
    /**
     * @return LengthAwarePaginator<int, InstitutionalEvent>
     */
    public function execute(ListInstitutionalEventsData $filters): LengthAwarePaginator
    {
        $query = InstitutionalEvent::query();

        if (filled($filters->title)) {
            $query->where('title', 'like', '%'.trim($filters->title).'%');
        }

        if ($filters->isActive !== null) {
            $query->where('is_active', $filters->isActive);
        }

        return $query
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
