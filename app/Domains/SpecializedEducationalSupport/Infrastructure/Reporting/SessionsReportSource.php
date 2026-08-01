<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;

final class SessionsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.sessions';
    }

    public function label(): string
    {
        return 'Atendimentos';
    }

    protected function model(): string
    {
        return Session::class;
    }

    protected function with(): array
    {
        return ['professional.person', 'creator'];
    }

    protected function definitions(): array
    {
        return [
            'professional' => ['label' => 'Profissional', 'path' => 'professional.person.name'],
            'session_date' => ['label' => 'Data', 'type' => ReportColumnType::DATE],
            'start_time' => ['label' => 'Início'], 'end_time' => ['label' => 'Fim'],
            'type' => ['label' => 'Tipo'], 'attendance_type' => ['label' => 'Modalidade'],
            'location' => ['label' => 'Local'], 'session_objective' => ['label' => 'Objetivo'],
            'status' => ['label' => 'Situação'], 'cancellation_reason' => ['label' => 'Motivo do cancelamento'],
            'creator' => ['label' => 'Criado por', 'path' => 'creator.name'],
        ];
    }

    protected function filterable(): array
    {
        return ['professional', 'session_date', 'type', 'attendance_type', 'location', 'status', 'creator'];
    }
}
