<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Pendencies;

use App\Domains\SpecializedEducationalSupport\Application\Data\Pendencies\ListPendenciesData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pendency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListPendenciesQuery
{
    /**
     * @return LengthAwarePaginator<int, Pendency>
     */
    public function execute(ListPendenciesData $filters): LengthAwarePaginator
    {
        return Pendency::query()
            ->with(['assignedProfessional.person', 'creator.professional.person'])
            ->when(filled($filters->title), static function ($query) use ($filters): void {
                $query->where('title', 'like', '%'.trim((string) $filters->title).'%');
            })
            ->when($filters->assignedTo !== null, static function ($query) use ($filters): void {
                $query->where('assigned_to', $filters->assignedTo);
            })
            ->when($filters->priority !== null, static function ($query) use ($filters): void {
                $query->where('priority', $filters->priority->value);
            })
            ->when($filters->isCompleted !== null, static function ($query) use ($filters): void {
                $query->where('is_completed', $filters->isCompleted);
            })
            ->latest()
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
