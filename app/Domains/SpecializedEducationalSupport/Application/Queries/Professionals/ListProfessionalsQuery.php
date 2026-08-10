<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Professionals;

use App\Domains\SpecializedEducationalSupport\Application\Data\Professionals\ListProfessionalsData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListProfessionalsQuery
{
    /**
     * @return LengthAwarePaginator<int, Professional>
     */
    public function execute(ListProfessionalsData $filters): LengthAwarePaginator
    {
        $query = Professional::query()
            ->select('professionals.*')
            ->join('people', 'people.id', '=', 'professionals.person_id')
            ->with(['person', 'position']);

        if (filled($filters->name)) {
            $query->where('people.name', 'like', '%'.trim($filters->name).'%');
        }

        if (filled($filters->email)) {
            $query->where('people.email', 'like', '%'.trim($filters->email).'%');
        }

        if (filled($filters->registration)) {
            $query->where('professionals.registration', 'like', '%'.trim($filters->registration).'%');
        }

        if ($filters->position !== null) {
            $query->where('professionals.position_id', $filters->position);
        }

        if ($filters->status !== null) {
            $query->where('professionals.status', $filters->status->value);
        }

        return $query
            ->orderBy('people.name')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
