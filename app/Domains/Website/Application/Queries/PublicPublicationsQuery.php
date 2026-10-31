<?php

declare(strict_types=1);

namespace App\Domains\Website\Application\Queries;

use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\Domain\Models\Tag;
use App\Domains\Website\Application\Data\ListPublicPublicationsData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class PublicPublicationsQuery
{
    private const PER_PAGE = 9;

    /**
     * @return LengthAwarePaginator<int, Publication>
     */
    public function execute(ListPublicPublicationsData $filters): LengthAwarePaginator
    {
        return Publication::query()
            ->publiclyVisible()
            ->when(filled($filters->q), function (Builder $query) use ($filters): void {
                $term = '%'.trim((string) $filters->q).'%';
                $query->where(fn (Builder $query) => $query
                    ->where('title', 'like', $term)
                    ->orWhere('summary', 'like', $term));
            })
            ->when(filled($filters->type), fn (Builder $query) => $query->where('type', $filters->type))
            ->when(filled($filters->tag), fn (Builder $query) => $query->whereHas(
                'tags',
                fn (Builder $tags) => $tags->where('is_active', true)->where('name', $filters->tag),
            ))
            ->with('tags')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    /**
     * @return list<string>
     */
    public function tags(): array
    {
        return Tag::query()
            ->where('is_active', true)
            ->whereHas('publications', fn (Builder $query) => $query->publiclyVisible())
            ->orderBy('name')
            ->pluck('name')
            ->all();
    }
}
