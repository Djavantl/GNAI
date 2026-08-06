<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Guardian;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;

final class StudentsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.students';
    }

    public function label(): string
    {
        return 'Alunos';
    }

    protected function model(): string
    {
        return Student::class;
    }

    protected function with(): array
    {
        return ['person', 'currentCourse.course'];
    }

    public function relationLabels(): array
    {
        return [
            'contexts' => 'Contextos dos alunos',
            'studentCourses' => 'Matrículas em cursos',
        ];
    }

    public function excludedRelations(): array
    {
        return ['person', 'currentContext', 'currentCourse'];
    }

    protected function definitions(): array
    {
        return [
            'name' => ['label' => 'Nome do aluno', 'path' => 'person.name'],
            'registration' => ['label' => 'Matrícula'],
            'status' => [
                'label' => 'Situação',
                'type' => ReportColumnType::SELECT,
                'options' => $this->enumOptions(StudentStatus::class),
            ],
            'entry_date' => ['label' => 'Data de ingresso', 'type' => ReportColumnType::DATE],
            'email' => ['label' => 'E-mail', 'path' => 'person.email'],
            'document' => ['label' => 'CPF', 'path' => 'person.document'],
            'birth_date' => [
                'label' => 'Data de nascimento',
                'path' => 'person.birth_date',
                'type' => ReportColumnType::DATE,
            ],
            'gender' => [
                'label' => 'Gênero',
                'path' => 'person.gender',
                'type' => ReportColumnType::SELECT,
                'options' => $this->enumOptions(Gender::class),
            ],
            'phone' => ['label' => 'Telefone', 'path' => 'person.phone'],
            'address' => ['label' => 'Endereço', 'path' => 'person.address'],
            'current_course' => ['label' => 'Curso atual', 'path' => 'currentCourse.course.name'],
        ];
    }

    protected function filterable(): array
    {
        return ['name', 'registration', 'status', 'entry_date', 'current_course'];
    }

    protected function relationPolicy(string $parentModel, string $relationName): array
    {
        $excluded = match ($parentModel) {
            Guardian::class => ['name'],
            Person::class => ['name', 'email', 'document', 'birth_date', 'gender', 'phone', 'address'],
            default => [],
        };

        return ['exclude' => $excluded];
    }
}
