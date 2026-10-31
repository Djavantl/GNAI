<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Tags;

use App\Domains\Inclusion\Application\Data\Tags\ListTagsData;
use App\Domains\Inclusion\Domain\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListTagsQuery
{
    /**
     * @return LengthAwarePaginator<int, Tag>
     */
    public function execute(ListTagsData $filters): LengthAwarePaginator
    {
        $query = Tag::query();

        if (filled($filters->name)) {
            $query->where('name', 'like', '%'.trim($filters->name).'%');
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
