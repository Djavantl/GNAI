<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Barriers;

use App\Domains\InclusiveRadar\Application\Data\Barriers\ListBarriersData;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final readonly class ListBarriersQuery
{
    public function execute(ListBarriersData $filters): LengthAwarePaginator
    {
        $name = trim((string) $filters->name);
        $barrierMorphClass = (new Barrier)->getMorphClass();

        return Barrier::query()
            ->with(['category', 'location', 'deficiencies', 'inspections.evidences', 'registeredBy'])
            ->when($name !== '', fn (Builder $query) => $query->where('name', 'like', "%{$name}%"))
            ->when($filters->category, function (Builder $query) use ($filters): void {
                $query->whereHas('category', function (Builder $subQuery) use ($filters): void {
                    $subQuery->where('name', 'like', "%{$filters->category}%");
                });
            })
            ->when($filters->priority, fn (Builder $query) => $query->where('priority', $filters->priority))
            ->when($filters->status, function (Builder $query) use ($filters, $barrierMorphClass): void {
                $query->whereExists(function ($subQuery) use ($filters, $barrierMorphClass): void {
                    $subQuery
                        ->selectRaw('1')
                        ->from('inspections as latest_barrier_inspections')
                        ->whereColumn('latest_barrier_inspections.inspectable_id', 'barriers.id')
                        ->where('latest_barrier_inspections.inspectable_type', $barrierMorphClass)
                        ->where('latest_barrier_inspections.status', $filters->status)
                        ->whereRaw(
                            <<<'SQL'
                            latest_barrier_inspections.id = (
                                select recent_barrier_inspections.id
                                from inspections as recent_barrier_inspections
                                where recent_barrier_inspections.inspectable_id = barriers.id
                                    and recent_barrier_inspections.inspectable_type = ?
                                order by recent_barrier_inspections.inspection_date desc,
                                    recent_barrier_inspections.created_at desc,
                                    recent_barrier_inspections.id desc
                                limit 1
                            )
                            SQL,
                            [$barrierMorphClass],
                        );
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }
}
