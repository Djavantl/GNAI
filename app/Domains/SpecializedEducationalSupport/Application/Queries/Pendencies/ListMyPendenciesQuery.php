<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Pendencies;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\Pendencies\ListPendenciesData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pendency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListMyPendenciesQuery
{
    /**
     * @return LengthAwarePaginator<int, Pendency>
     */
    public function execute(ListPendenciesData $filters, User $user): LengthAwarePaginator
    {
        return Pendency::query()
            ->with(['assignedProfessional.person', 'creator.professional.person'])
            ->where('assigned_to', $user->professional_id ?? 0)
            ->when(filled($filters->title), static function ($query) use ($filters): void {
                $query->where('title', 'like', '%'.trim((string) $filters->title).'%');
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
