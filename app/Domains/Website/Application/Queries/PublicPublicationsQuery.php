<?php

declare(strict_types=1);

namespace App\Domains\Website\Application\Queries;

use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\Domain\Models\Tag;
use App\Domains\Website\Application\Data\ListPublicPublicationsData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class PublicPublicationsQuery
{
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
            ->paginate(9)
            ->withQueryString();
    }

    public function findVisible(string $slug): Publication
    {
        return Publication::query()
            ->publiclyVisible()
            ->where('slug', $slug)
            ->with(['author', 'media', 'speakers.professional.person', 'speakers.professional.position', 'tags'])
            ->firstOrFail();
    }

    /**
     * Até três publicações visíveis do mesmo tipo, das mais recentes para as mais antigas.
     *
     * @return Collection<int, Publication>
     */
    public function related(Publication $publication): Collection
    {
        return Publication::query()
            ->publiclyVisible()
            ->whereKeyNot($publication->getKey())
            ->where('type', $publication->type)
            ->with('tags')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(3)
            ->get();
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
