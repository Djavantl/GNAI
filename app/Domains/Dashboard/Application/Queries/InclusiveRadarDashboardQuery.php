<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Application\Queries;

use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use Illuminate\Support\Collection;

final readonly class InclusiveRadarDashboardQuery
{
    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        return [
            'totalAt' => $this->totalActiveAssistiveTechnologies(),
            'totalAem' => $this->totalActiveAccessibleEducationalMaterials(),
            'totalLoans' => $this->totalLoans(),
            'totalWaiting' => $this->totalWaitingAndNotifiedWaitlists(),
            'totalBarriers' => $this->totalBarriers(),
            'barrierStatusCounts' => $this->barrierStatusCounts(),
            'mapBarriers' => $this->mapBarriers(),
        ];
    }

    private function totalActiveAssistiveTechnologies(): int
    {
        return AssistiveTechnology::query()
            ->where('is_active', true)
            ->count();
    }

    private function totalActiveAccessibleEducationalMaterials(): int
    {
        return AccessibleEducationalMaterial::query()
            ->where('is_active', true)
            ->count();
    }

    private function totalLoans(): int
    {
        return Loan::count();
    }

    private function totalWaitingAndNotifiedWaitlists(): int
    {
        return Waitlist::whereIn('status', [
            WaitlistStatus::WAITING->value,
            WaitlistStatus::NOTIFIED->value,
        ])->count();
    }

    private function totalBarriers(): int
    {
        return Barrier::count();
    }

    /**
     * @return Collection<int, array{label: string, color: string, count: int}>
     */
    private function barrierStatusCounts(): Collection
    {
        $barriers = Barrier::query()->get();

        return collect(BarrierStatus::cases())
            ->map(fn (BarrierStatus $status): array => [
                'label' => $status->label(),
                'color' => $status->color(),
                'count' => $barriers
                    ->filter(fn (Barrier $barrier): bool => $barrier->latestStatus() === $status)
                    ->count(),
            ])
            ->filter(fn (array $item): bool => $item['count'] > 0)
            ->values();
    }

    /**
     * @return Collection<int, array{
     *     id: int,
     *     name: string,
     *     lat: float,
     *     lng: float,
     *     status: string,
     *     status_label: string,
     *     blocks_map: bool,
     *     category_name: string,
     *     color: string,
     *     url: string
     * }>
     */
    private function mapBarriers(): Collection
    {
        return Barrier::with(['category', 'location', 'institution', 'inspections'])
            ->get()
            ->map(function (Barrier $barrier): ?array {
                $currentStatus = $barrier->latestStatus();

                if (! $currentStatus) {
                    return null;
                }

                return [
                    'id' => $barrier->id,
                    'name' => $barrier->name,
                    'lat' => (float) $barrier->latitude,
                    'lng' => (float) $barrier->longitude,
                    'status' => $currentStatus->value,
                    'status_label' => $currentStatus->label(),
                    'blocks_map' => (bool) ($barrier->category?->blocks_map ?? false),
                    'category_name' => $barrier->category?->name ?? 'Sem Categoria',
                    'color' => $currentStatus->color(),
                    'url' => route('inclusive-radar.barriers.show', $barrier),
                ];
            })
            ->filter()
            ->values();
    }
}
