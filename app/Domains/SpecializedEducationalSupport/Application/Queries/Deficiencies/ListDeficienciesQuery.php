<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Deficiencies;

use App\Domains\SpecializedEducationalSupport\Application\Data\Deficiencies\ListDeficienciesData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListDeficienciesQuery
{
    /**
     * @return LengthAwarePaginator<int, Deficiency>
     */
    public function execute(ListDeficienciesData $filters): LengthAwarePaginator
    {
        $query = Deficiency::query();

        if (filled($filters->name)) {
            $query->where('name', 'like', trim($filters->name).'%');
        }

        if (filled($filters->cidCode)) {
            $query->where('cid_code', 'like', trim($filters->cidCode).'%');
        }

        if ($filters->isActive !== null) {
            $query->where('is_active', $filters->isActive);
        }

        return $query
            ->orderBy('name')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
