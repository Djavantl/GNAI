<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\ProfessionalStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;

final class ProfessionalsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.professionals';
    }

    public function label(): string
    {
        return 'Profissionais';
    }

    protected function model(): string
    {
        return Professional::class;
    }

    protected function with(): array
    {
        return ['person', 'position'];
    }

    protected function definitions(): array
    {
        return [
            'name' => ['label' => 'Nome', 'path' => 'person.name'],
            'registration' => ['label' => 'Matrícula'],
            'position' => ['label' => 'Cargo', 'path' => 'position.name'],
            'entry_date' => ['label' => 'Data de ingresso', 'type' => ReportColumnType::DATE],
            'status' => ['label' => 'Situação', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(ProfessionalStatus::class)],
            'email' => ['label' => 'E-mail', 'path' => 'person.email'],
            'phone' => ['label' => 'Telefone', 'path' => 'person.phone'],
        ];
    }

    protected function filterable(): array
    {
        return ['name', 'registration', 'position', 'entry_date', 'status'];
    }

    protected function relationPolicy(string $parentModel, string $relationName): array
    {
        $excluded = match ($parentModel) {
            User::class => ['name', 'email'],
            Person::class => ['name', 'email', 'phone'],
            default => [],
        };

        return ['exclude' => $excluded];
    }
}
