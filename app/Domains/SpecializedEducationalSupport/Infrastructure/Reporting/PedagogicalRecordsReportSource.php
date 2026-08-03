<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PedagogicalRecord;

final class PedagogicalRecordsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.pedagogical-records';
    }

    public function label(): string
    {
        return 'Registros pedagógicos';
    }

    protected function model(): string
    {
        return PedagogicalRecord::class;
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
            'is_present' => ['label' => 'Aluno presente', 'type' => ReportColumnType::BOOLEAN],
            'absence_reason' => ['label' => 'Motivo da ausência'],
            'planned_performed_activities' => ['label' => 'Atividades planejadas e realizadas'],
            'pedagogical_record' => ['label' => 'Registro pedagógico'],
            'resources_used' => ['label' => 'Recursos utilizados'],
            'general_observations' => ['label' => 'Observações gerais'],
            'created_at' => ['label' => 'Criado em', 'type' => ReportColumnType::DATE],
        ];
    }

    protected function filterable(): array
    {
        return ['professional', 'session_date', 'duration', 'is_present', 'created_at'];
    }
}
