<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Professionals;

use App\Domains\Inclusion\Domain\Enums\Gender;
use App\Domains\Inclusion\Domain\Enums\ProfessionalStatus;
use App\Domains\Inclusion\Domain\Models\Position;
use App\Domains\Inclusion\Domain\Models\Professional;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final class ProfessionalFormQuery
{
    public function forIndex(): array
    {
        return [
            'positions' => $this->positionOptions(),
            'professionalStatuses' => $this->statusOptions(),
        ];
    }

    public function forCreation(bool $canManageAdministrators): array
    {
        return $this->formOptions() + [
            'defaultGender' => Gender::NOT_SPECIFIED->value,
            'canManageAdministrators' => $canManageAdministrators,
        ];
    }

    public function forUpdate(Professional $professional, bool $canManageAdministrators): array
    {
        $professional->loadMissing(['person', 'position', 'user']);
        $professional->person?->append('photo_url');

        return $this->formOptions() + [
            'professional' => $professional,
            'defaultStatus' => ProfessionalStatus::ACTIVE->value,
            'canManageAdministrators' => $canManageAdministrators,
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

    private function statusOptions(): SupportCollection
    {
        return collect(ProfessionalStatus::cases())
            ->mapWithKeys(
                static fn (ProfessionalStatus $status): array => [
                    $status->value => $status->label(),
                ],
            );
    }
}
