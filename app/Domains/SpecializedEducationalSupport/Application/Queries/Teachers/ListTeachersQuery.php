<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Teachers;

use App\Domains\SpecializedEducationalSupport\Application\Data\Teachers\ListTeachersData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListTeachersQuery
{
    /**
     * @return LengthAwarePaginator<int, Teacher>
     */
    public function execute(ListTeachersData $filters): LengthAwarePaginator
    {
        return Teacher::query()
            ->with('person')
            ->when(
                $filters->name,
                fn (Builder $query, string $term): Builder => $query->whereHas(
                    'person',
                    fn (Builder $query): Builder => $query->where('name', 'like', "%{$term}%"),
                ),
            )
            ->when(
                $filters->email,
                fn (Builder $query, string $term): Builder => $query->whereHas(
                    'person',
                    fn (Builder $query): Builder => $query->where('email', 'like', "%{$term}%"),
                ),
            )
            ->when(
                $filters->registration,
                fn (Builder $query, string $term): Builder => $query->where('registration', 'like', "%{$term}%"),
            )
            ->orderByDesc('id')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
