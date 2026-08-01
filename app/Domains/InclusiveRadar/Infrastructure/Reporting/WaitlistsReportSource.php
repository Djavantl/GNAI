<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Reporting;

use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use App\Domains\Reporting\Application\Services\ReportOptionCatalog;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class WaitlistsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'inclusive-radar.waitlists';
    }

    public function label(): string
    {
        return 'Listas de espera';
    }

    protected function model(): string
    {
        return Waitlist::class;
    }

    protected function with(): array
    {
        return ['waitlistable', 'student.person', 'professional.person', 'user'];
    }

    protected function definitions(): array
    {
        return [
            'waitlistable_type' => [
                'label' => 'Tipo de recurso',
                'type' => ReportColumnType::SELECT,
                'options' => ReportOptionCatalog::loanableTypes(),
            ],
            'waitlistable' => ['label' => 'Recurso', 'path' => 'waitlistable.name'],
            'student' => ['label' => 'Aluno', 'path' => 'student.person.name'],
            'professional' => ['label' => 'Profissional', 'path' => 'professional.person.name'],
            'requested_at' => ['label' => 'Solicitado em', 'type' => ReportColumnType::DATE],
            'status' => ['label' => 'Situação', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(WaitlistStatus::class)],
            'observation' => ['label' => 'Observação'], 'registered_by' => ['label' => 'Registrado por', 'path' => 'user.name'],
        ];
    }

    protected function filterable(): array
    {
        return ['waitlistable_type', 'student', 'professional', 'requested_at', 'status', 'registered_by'];
    }
}
