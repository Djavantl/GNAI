<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;

final class StudentCoursesReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.student-courses';
    }

    public function label(): string
    {
        return 'Matrículas em cursos';
    }

    protected function model(): string
    {
        return StudentCourse::class;
    }

    protected function with(): array
    {
        return ['student.person', 'course', 'failedDisciplines', 'atRiskDisciplines'];
    }

    public function excludedRelations(): array
    {
        return ['failedDisciplines', 'atRiskDisciplines'];
    }

    protected function definitions(): array
    {
        return [
            'student' => ['label' => 'Aluno', 'path' => 'student.person.name'],
            'registration' => ['label' => 'Matrícula', 'path' => 'student.registration'],
            'course' => ['label' => 'Curso', 'path' => 'course.name'],
            'academic_year' => ['label' => 'Ano letivo'],
            'is_current' => ['label' => 'Matrícula atual', 'type' => ReportColumnType::BOOLEAN],
            'school_attendance_status' => ['label' => 'Situação da frequência escolar'],
            'failed_disciplines' => ['label' => 'Disciplinas com reprovação', 'path' => 'failed_discipline_names'],
            'at_risk_disciplines' => ['label' => 'Disciplinas com risco de insucesso acadêmico', 'path' => 'at_risk_discipline_names'],
        ];
    }

    protected function filterable(): array
    {
        return ['student', 'registration', 'course', 'academic_year', 'is_current'];
    }

    protected function relationPolicy(string $parentModel, string $relationName): array
    {
        $excluded = match ($parentModel) {
            Student::class => ['student', 'registration'],
            Course::class => ['course'],
            default => [],
        };

        return ['exclude' => $excluded];
    }
}
