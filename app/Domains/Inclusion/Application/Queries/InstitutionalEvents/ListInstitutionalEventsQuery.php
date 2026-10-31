<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\InstitutionalEvents;

use App\Domains\Inclusion\Application\Data\InstitutionalEvents\ListInstitutionalEventsData;
use App\Domains\Inclusion\Domain\Models\InstitutionalEvent;
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

        if (filled($filters->type)) {
            $query->where('type', $filters->type);
        }

        return $query
            ->with('author')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->orderByDesc('id')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
