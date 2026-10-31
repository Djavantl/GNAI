<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Domain\Models;

use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Enums\LoanStatus;
use App\Domains\Inclusion\Domain\Exceptions\InvalidLoanableResource;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Loan;
use Tests\TestCase;

final class LoanPresentationTest extends TestCase
{
    public function test_it_presents_an_active_loan_past_due_date_as_overdue(): void
    {
        $loan = new Loan([
            'status' => LoanStatus::ACTIVE,
            'due_date' => now()->subDay(),
        ]);

        self::assertTrue($loan->isOverdue());
        self::assertSame('Em Atraso', $loan->statusLabel());
        self::assertSame('danger', $loan->statusColor());
    }

    public function test_it_does_not_present_a_returned_loan_as_overdue(): void
    {
        $loan = new Loan([
            'status' => LoanStatus::RETURNED,
            'due_date' => now()->subDay(),
        ]);

        self::assertFalse($loan->isOverdue());
        self::assertTrue($loan->isReturned());
        self::assertSame(LoanStatus::RETURNED->label(), $loan->statusLabel());
    }

    public function test_it_takes_the_loanable_type_from_the_loaned_item(): void
    {
        $loan = (new Loan)->setRelation('loanable', new Item([
            'item_type' => ItemType::AccessibleEducationalMaterial,
        ]));

        self::assertSame(ItemType::AccessibleEducationalMaterial, $loan->loanableType());
    }

    public function test_it_rejects_loanable_type_without_a_loaned_item(): void
    {
        $loan = (new Loan)->setRelation('loanable', null);

        $this->expectException(InvalidLoanableResource::class);

        $loan->loanableType();
    }
}
