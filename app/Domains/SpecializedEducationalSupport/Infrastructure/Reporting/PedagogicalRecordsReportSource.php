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
        return [
            'attendanceSession.students.person',
            'attendanceSession.professional.person',
        ];
    }

    public function excludedRelations(): array
    {
        return [];
    }

    protected function definitions(): array
    {
        return [
            'student' => ['label' => 'Estudante', 'path' => 'attendanceSession.students.0.person.name'],
            'professional' => ['label' => 'Profissional', 'path' => 'attendanceSession.professional.person.name'],
            'session_date' => ['label' => 'Data do atendimento', 'path' => 'attendanceSession.session_date', 'type' => ReportColumnType::DATE],
            'follow_up_reason' => ['label' => 'Motivo do acompanhamento'],
            'duration' => ['label' => 'Período/duração do acompanhamento'],
            'is_present' => ['label' => 'Presença do estudante no atendimento', 'type' => ReportColumnType::BOOLEAN],
            'absence_reason' => ['label' => 'Motivo da ausência'],
            'systematic_pedagogical_follow_up_record' => ['label' => 'Registro do acompanhamento pedagógico sistemático'],
            'strategies_and_resources_adopted' => ['label' => 'Estratégias e recursos adotados'],
            'referrals_made' => ['label' => 'Encaminhamentos realizados'],
            'complementary_observations' => ['label' => 'Observações complementares'],
            'created_at' => ['label' => 'Criado em', 'type' => ReportColumnType::DATE],
        ];
    }

    protected function filterable(): array
    {
        return ['professional', 'session_date', 'duration', 'is_present', 'created_at'];
    }
}
