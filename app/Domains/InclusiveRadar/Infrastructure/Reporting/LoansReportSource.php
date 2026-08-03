<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Reporting;

use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\Reporting\Application\Services\ReportOptionCatalog;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class LoansReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'inclusive-radar.loans';
    }

    public function label(): string
    {
        return 'Empréstimos';
    }

    protected function model(): string
    {
        return Loan::class;
    }

    protected function with(): array
    {
        return ['loanable', 'student.person', 'professional.person', 'user'];
    }

    protected function definitions(): array
    {
        return [
            'loanable_type' => [
                'label' => 'Tipo de recurso',
                'type' => ReportColumnType::SELECT,
                'options' => ReportOptionCatalog::loanableTypes(),
            ],
            'loanable' => ['label' => 'Recurso', 'path' => 'loanable.name'],
            'student' => ['label' => 'Aluno', 'path' => 'student.person.name'],
            'professional' => ['label' => 'Profissional', 'path' => 'professional.person.name'],
            'loan_date' => ['label' => 'Emprestado em', 'type' => ReportColumnType::DATE],
            'due_date' => ['label' => 'Devolução prevista', 'type' => ReportColumnType::DATE],
            'return_date' => ['label' => 'Devolvido em', 'type' => ReportColumnType::DATE],
            'status' => ['label' => 'Situação', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(LoanStatus::class)],
            'observation' => ['label' => 'Observação'], 'registered_by' => ['label' => 'Registrado por', 'path' => 'user.name'],
        ];
    }

    protected function filterable(): array
    {
        return ['loanable_type', 'student', 'professional', 'loan_date', 'due_date', 'return_date', 'status', 'registered_by'];
    }
}
