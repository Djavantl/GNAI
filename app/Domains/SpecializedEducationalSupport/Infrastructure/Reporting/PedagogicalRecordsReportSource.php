<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\PedagogicalFollowUpStatus;
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
            'failedDisciplines',
            'atRiskDisciplines',
        ];
    }

    public function excludedRelations(): array
    {
        return ['failedDisciplines', 'atRiskDisciplines'];
    }

    protected function definitions(): array
    {
        return [
            'student' => ['label' => 'Estudante', 'path' => 'attendanceSession.students.0.person.name'],
            'professional' => ['label' => 'Profissional', 'path' => 'attendanceSession.professional.person.name'],
            'session_date' => ['label' => 'Data do atendimento', 'path' => 'attendanceSession.session_date', 'type' => ReportColumnType::DATE],
            'follow_up_reason' => ['label' => 'Motivo do acompanhamento'],
            'follow_up_status' => [
                'label' => 'Situação do acompanhamento',
                'type' => ReportColumnType::SELECT,
                'options' => $this->enumOptions(PedagogicalFollowUpStatus::class),
            ],
            'duration' => ['label' => 'Período/duração do acompanhamento'],
            'is_present' => ['label' => 'Presença do estudante no atendimento', 'type' => ReportColumnType::BOOLEAN],
            'absence_reason' => ['label' => 'Motivo da ausência'],
            'systematic_pedagogical_follow_up_record' => ['label' => 'Registro do acompanhamento pedagógico sistemático'],
            'strategies_and_resources_adopted' => ['label' => 'Estratégias e recursos adotados'],
            'failed_disciplines' => ['label' => 'Disciplinas com reprovação', 'path' => 'failed_discipline_names'],
            'at_risk_disciplines' => ['label' => 'Disciplinas com risco de insucesso acadêmico', 'path' => 'at_risk_discipline_names'],
            'school_attendance_status' => ['label' => 'Situação da frequência escolar'],
            'referrals_made' => ['label' => 'Encaminhamentos realizados'],
            'complementary_observations' => ['label' => 'Observações complementares'],
            'created_at' => ['label' => 'Criado em', 'type' => ReportColumnType::DATE],
        ];
    }

    protected function filterable(): array
    {
        return ['professional', 'session_date', 'follow_up_status', 'duration', 'is_present', 'created_at'];
    }
}
