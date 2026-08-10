<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\Models;

use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use Tests\TestCase;

final class LoanPresentationTest extends TestCase
{
    public function test_it_presents_overdue_status(): void
    {
        $loan = new Loan([
            'status' => LoanStatus::ACTIVE,
            'due_date' => now()->subDay(),
        ]);

        self::assertTrue($loan->isOverdue());
        self::assertTrue($loan->isActive());
        self::assertSame('Em Atraso', $loan->statusLabel());
        self::assertSame('danger', $loan->statusColor());
    }

    public function test_it_presents_returned_status(): void
    {
        $loan = new Loan([
            'status' => LoanStatus::RETURNED,
            'due_date' => now()->subDay(),
        ]);

        self::assertFalse($loan->isOverdue());
        self::assertTrue($loan->isReturned());
        self::assertSame('Devolvido (No prazo)', $loan->statusLabel());
        self::assertSame('primary', $loan->statusColor());
    }

    public function test_it_presents_loanable_type_metadata(): void
    {
        $loan = new Loan([
            'loanable_type' => 'assistive_technology',
        ]);

        self::assertSame('Tecnologia Assistiva', $loan->loanableType()->label());
        self::assertSame('fa-microchip', $loan->loanableType()->icon());
    }
}
