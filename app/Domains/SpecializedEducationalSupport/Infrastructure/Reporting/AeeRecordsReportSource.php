<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeRecord;

final class AeeRecordsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.aee-records';
    }

    public function label(): string
    {
        return 'Registros AEE';
    }

    protected function model(): string
    {
        return AeeRecord::class;
    }

    protected function with(): array
    {
        return ['attendanceSession.professional.person'];
    }

    protected function definitions(): array
    {
        return [
            'professional' => ['label' => 'Profissional', 'path' => 'attendanceSession.professional.person.name'],
            'session_date' => ['label' => 'Data do atendimento', 'path' => 'attendanceSession.session_date', 'type' => ReportColumnType::DATE],
            'duration' => ['label' => 'Duração'],
            'activities_performed' => ['label' => 'Atividades realizadas'],
            'strategies_used' => ['label' => 'Estratégias utilizadas'],
            'resources_used' => ['label' => 'Recursos utilizados'],
            'general_observations' => ['label' => 'Observações gerais'],
            'created_at' => ['label' => 'Criado em', 'type' => ReportColumnType::DATE],
        ];
    }

    protected function filterable(): array
    {
        return ['professional', 'session_date', 'duration', 'created_at'];
    }
}
