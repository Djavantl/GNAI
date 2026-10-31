<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Domain\Models;

use App\Domains\Inclusion\Domain\DTOs\Loans\CreateLoanDTO;
use App\Domains\Inclusion\Domain\DTOs\Loans\ReturnLoanDTO;
use App\Domains\Inclusion\Domain\Enums\LoanStatus;
use App\Domains\Inclusion\Domain\Exceptions\InvalidLoan;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Loan;
use Tests\TestCase;

final class LoanTest extends TestCase
{
    public function test_it_registers_an_active_loan_for_an_item(): void
    {
        $loan = Loan::register(new CreateLoanDTO(
            loanableId: 10,
            loanableType: (new Item)->getMorphClass(),
            studentId: 20,
            professionalId: null,
            registeredBy: 30,
            loanDate: '2026-07-19 10:00:00',
            dueDate: '2026-07-24',
            observation: 'Entrega inicial.',
        ));

        self::assertSame(10, $loan->loanable_id);
        self::assertSame('item', $loan->loanable_type);
        self::assertSame(20, $loan->student_id);
        self::assertSame(30, $loan->user_id);
        self::assertSame(LoanStatus::ACTIVE, $loan->status);
        self::assertNull($loan->return_date);
    }

    public function test_it_registers_return_keeping_observation_when_none_is_given(): void
    {
        $loan = new Loan([
            'status' => LoanStatus::ACTIVE,
            'return_date' => null,
            'observation' => 'Sem avaria.',
        ]);

        $loan->registerReturn(new ReturnLoanDTO(
            returnDate: now(),
            status: LoanStatus::RETURNED,
            observation: null,
        ));

        self::assertSame(LoanStatus::RETURNED, $loan->status);
        self::assertNotNull($loan->return_date);
        self::assertSame('Sem avaria.', $loan->observation);
    }

    public function test_it_rejects_registering_return_twice(): void
    {
        $loan = new Loan([
            'return_date' => now(),
        ]);

        $this->expectException(InvalidLoan::class);
        $this->expectExceptionMessage('Este empréstimo já foi finalizado.');

        $loan->registerReturn(new ReturnLoanDTO(
            returnDate: now(),
            status: LoanStatus::RETURNED,
            observation: null,
        ));
    }

    public function test_it_calculates_returned_status(): void
    {
        $dueDate = now();

        self::assertSame(
            LoanStatus::DAMAGED,
            Loan::returnedStatus(isDamaged: true, returnDate: $dueDate->copy()->addDay(), dueDate: $dueDate),
        );
        self::assertSame(
            LoanStatus::LATE,
            Loan::returnedStatus(isDamaged: false, returnDate: $dueDate->copy()->addSecond(), dueDate: $dueDate),
        );
        self::assertSame(
            LoanStatus::RETURNED,
            Loan::returnedStatus(isDamaged: false, returnDate: $dueDate, dueDate: $dueDate),
        );
    }
}
