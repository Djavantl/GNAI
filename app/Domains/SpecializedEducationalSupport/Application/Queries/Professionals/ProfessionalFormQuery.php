<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Professionals;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\ProfessionalStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Position;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use Illuminate\Database\Eloquent\Collection;

final class ProfessionalFormQuery
{
    public function forIndex(): array
    {
        return [
            'positions' => $this->positionOptions(),
            'professionalStatuses' => $this->statusOptions(),
        ];
    }

    public function forCreation(): array
    {
        return $this->formOptions() + [
            'defaultGender' => Gender::NOT_SPECIFIED->value,
        ];
    }

    public function forUpdate(Professional $professional): array
    {
        return $this->formOptions() + [
            'professional' => $professional->loadMissing(['person', 'position', 'user']),
            'defaultStatus' => ProfessionalStatus::ACTIVE->value,
        ];
    }

    private function positionOptions(): Collection
    {
        return Position::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function formOptions(): array
    {
        return [
            'positions' => Position::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
            'genders' => collect(Gender::cases())
                ->mapWithKeys(
                    static fn (Gender $gender): array => [
                        $gender->value => $gender->label(),
                    ],
                ),
            'professionalStatuses' => $this->statusOptions(),
        ];
    }

    private function statusOptions(): \Illuminate\Support\Collection
    {
        return collect(ProfessionalStatus::cases())
            ->mapWithKeys(
                static fn (ProfessionalStatus $status): array => [
                    $status->value => $status->label(),
                ],
            );
    }
}
