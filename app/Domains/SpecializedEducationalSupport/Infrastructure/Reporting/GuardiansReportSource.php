<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\GuardianRelationship;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Guardian;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;

final class GuardiansReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.guardians';
    }

    public function label(): string
    {
        return 'Responsáveis';
    }

    protected function model(): string
    {
        return Guardian::class;
    }

    protected function with(): array
    {
        return ['person', 'student.person'];
    }

    protected function definitions(): array
    {
        return [
            'student' => ['label' => 'Aluno', 'path' => 'student.person.name'],
            'name' => ['label' => 'Responsável', 'path' => 'person.name'],
            'relationship' => ['label' => 'Parentesco', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(GuardianRelationship::class)],
            'document' => ['label' => 'CPF', 'path' => 'person.document'],
            'email' => ['label' => 'E-mail', 'path' => 'person.email'],
            'phone' => ['label' => 'Telefone', 'path' => 'person.phone'],
        ];
    }

    protected function filterable(): array
    {
        return ['student', 'name', 'relationship', 'document'];
    }

    protected function relationPolicy(string $parentModel, string $relationName): array
    {
        $excluded = match ($parentModel) {
            Student::class => ['student'],
            Person::class => ['name', 'document', 'email', 'phone'],
            default => [],
        };

        return ['exclude' => $excluded];
    }
}
