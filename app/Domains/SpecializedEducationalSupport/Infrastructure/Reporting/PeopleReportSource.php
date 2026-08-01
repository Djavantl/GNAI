<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Guardian;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;

final class PeopleReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.people';
    }

    public function label(): string
    {
        return 'Pessoas';
    }

    protected function model(): string
    {
        return Person::class;
    }

    protected function definitions(): array
    {
        return [
            'name' => ['label' => 'Nome'], 'document' => ['label' => 'CPF'],
            'birth_date' => ['label' => 'Data de nascimento', 'type' => ReportColumnType::DATE],
            'gender' => ['label' => 'Gênero', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(Gender::class)],
            'email' => ['label' => 'E-mail'], 'phone' => ['label' => 'Telefone'],
            'address' => ['label' => 'Endereço'],
        ];
    }

    protected function filterable(): array
    {
        return ['name', 'document', 'birth_date', 'gender', 'email'];
    }

    protected function relationPolicy(string $parentModel, string $relationName): array
    {
        $excluded = match ($parentModel) {
            Guardian::class => ['name', 'document', 'email', 'phone'],
            Student::class => ['name', 'document', 'birth_date', 'gender', 'email', 'phone', 'address'],
            Professional::class, Teacher::class => ['name', 'email', 'phone'],
            default => [],
        };

        return ['exclude' => $excluded];
    }
}
