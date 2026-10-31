<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Presenters;

use App\Domains\Inclusion\Application\Data\Pendencies\ListPendenciesData;
use App\Domains\Inclusion\Domain\Enums\Priority;
use App\Domains\Inclusion\Domain\Models\Professional;
use Illuminate\Support\Collection;

final class PendencyListPresenter
{
    /**
     * @return array{title: string, priority: string, is_completed: string}
     */
    public function filters(ListPendenciesData $filters): array
    {
        return [
            'title' => $filters->title ?? '',
            'priority' => $filters->priority?->value ?? '',
            'is_completed' => $filters->isCompleted === null ? '' : (string) (int) $filters->isCompleted,
        ];
    }

    /**
     * @param  Collection<int, Professional>  $professionals
     * @return Collection<int, array{value: string, label: string}>
     */
    public function professionalOptions(Collection $professionals): Collection
    {
        return $professionals
            ->map(static fn (Professional $professional): array => [
                'value' => (string) $professional->getKey(),
                'label' => (string) ($professional->person?->name ?? ''),
            ])
            ->values();
    }

    /**
     * @return list<array{value: string, label: string, color: string}>
     */
    public function priorityOptions(): array
    {
        return array_map(
            static fn (Priority $priority): array => [
                'value' => $priority->value,
                'label' => $priority->label(),
                'color' => $priority->color(),
            ],
            Priority::cases(),
        );
    }
}
