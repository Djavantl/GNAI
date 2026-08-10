<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\Loans\CreateLoanDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Loans\ReturnLoanDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Loans\UpdateLoanDTO;
use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidLoan;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use Tests\TestCase;

final class LoanTest extends TestCase
{
    public function test_it_casts_status_to_domain_enum(): void
    {
        $loan = new Loan([
            'status' => LoanStatus::ACTIVE,
        ]);

        $this->assertSame(LoanStatus::ACTIVE, $loan->status);
    }

    public function test_it_registers_an_active_loan(): void
    {
        $loan = Loan::register(new CreateLoanDTO(
            loanableId: 10,
            loanableType: LoanableType::AssistiveTechnology,
            studentId: 20,
            professionalId: null,
            registeredBy: 30,
            loanDate: '2026-07-19 10:00:00',
            dueDate: '2026-07-24',
            observation: 'Entrega inicial.',
        ));

        $this->assertSame(10, $loan->loanable_id);
        $this->assertSame('assistive_technology', $loan->loanable_type);
        $this->assertSame(20, $loan->student_id);
        $this->assertNull($loan->professional_id);
        $this->assertSame(30, $loan->user_id);
        $this->assertSame(LoanStatus::ACTIVE, $loan->status);
        $this->assertNull($loan->return_date);
        $this->assertSame('Entrega inicial.', $loan->observation);
    }

    public function test_it_revises_observation(): void
    {
        $loan = new Loan([
            'observation' => 'Antiga.',
        ]);

        $loan->revise(new UpdateLoanDTO(
            observation: 'Nova.',
        ));

        $this->assertSame('Nova.', $loan->observation);
    }

    public function test_it_registers_return(): void
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

        $this->assertSame(LoanStatus::RETURNED, $loan->status);
        $this->assertNotNull($loan->return_date);
        $this->assertSame('Sem avaria.', $loan->observation);
    }

    public function test_it_calculates_returned_status(): void
    {
        $dueDate = now();

        $this->assertSame(
            LoanStatus::DAMAGED,
            Loan::returnedStatus(
                isDamaged: true,
                returnDate: $dueDate,
                dueDate: $dueDate,
            ),
        );
        $this->assertSame(
            LoanStatus::LATE,
            Loan::returnedStatus(
                isDamaged: false,
                returnDate: $dueDate->copy()->addSecond(),
                dueDate: $dueDate,
            ),
        );
        $this->assertSame(
            LoanStatus::RETURNED,
            Loan::returnedStatus(
                isDamaged: false,
                returnDate: $dueDate,
                dueDate: $dueDate,
            ),
        );
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
}
