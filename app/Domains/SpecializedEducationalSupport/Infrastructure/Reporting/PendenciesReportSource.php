<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pendency;

final class PendenciesReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.pendencies';
    }

    public function label(): string
    {
        return 'Pendências';
    }

    protected function model(): string
    {
        return Pendency::class;
    }

    protected function with(): array
    {
        return ['creator', 'assignedProfessional.person'];
    }

    protected function definitions(): array
    {
        return [
            'title' => ['label' => 'Título'], 'description' => ['label' => 'Descrição'],
            'priority' => ['label' => 'Prioridade'],
            'due_date' => ['label' => 'Prazo', 'type' => ReportColumnType::DATE],
            'is_completed' => ['label' => 'Concluída', 'type' => ReportColumnType::BOOLEAN],
            'creator' => ['label' => 'Criada por', 'path' => 'creator.name'],
            'assigned_professional' => ['label' => 'Responsável', 'path' => 'assignedProfessional.person.name'],
            'created_at' => ['label' => 'Criada em', 'type' => ReportColumnType::DATE],
        ];
    }

    protected function filterable(): array
    {
        return ['title', 'priority', 'due_date', 'is_completed', 'creator', 'assigned_professional'];
    }
}
