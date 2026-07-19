<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\UI\Presenters;

use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\InclusiveRadar\UI\Presenters\LoanPresenter;
use Tests\TestCase;

final class LoanPresenterTest extends TestCase
{
    public function test_it_presents_overdue_status(): void
    {
        $loan = new Loan([
            'status' => LoanStatus::ACTIVE,
            'due_date' => now()->subDay(),
        ]);

        $this->assertTrue(LoanPresenter::isOverdue($loan));
        $this->assertTrue(LoanPresenter::isActive($loan));
        $this->assertSame('Em Atraso', LoanPresenter::statusLabel($loan));
        $this->assertSame('danger', LoanPresenter::statusColor($loan));
    }

    public function test_it_presents_returned_status(): void
    {
        $loan = new Loan([
            'status' => LoanStatus::RETURNED,
            'due_date' => now()->subDay(),
        ]);

        $this->assertFalse(LoanPresenter::isOverdue($loan));
        $this->assertTrue(LoanPresenter::isReturned($loan));
        $this->assertSame('Devolvido (No prazo)', LoanPresenter::statusLabel($loan));
        $this->assertSame('primary', LoanPresenter::statusColor($loan));
    }

    public function test_it_presents_loanable_type_metadata(): void
    {
        $loan = new Loan([
            'loanable_type' => 'assistive_technology',
        ]);

        $this->assertSame('Tecnologia Assistiva', LoanPresenter::loanableTypeLabel($loan));
        $this->assertSame('fa-microchip', LoanPresenter::loanableIcon($loan));
    }
}
