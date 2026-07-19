<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Loans;

use App\Domains\InclusiveRadar\Application\Data\Loans\CreateLoanData;
use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use Tests\TestCase;

final class CreateLoanDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = CreateLoanData::from([
            'loanable_id' => 10,
            'loanable_type' => 'assistive_technology',
            'student_id' => 20,
            'loan_date' => '2026-07-19 10:00:00',
            'due_date' => '2026-07-24',
            'observation' => 'Entrega inicial.',
        ]);

        $this->assertSame(10, $data->loanableId);
        $this->assertSame(LoanableType::AssistiveTechnology, $data->loanableType);
        $this->assertSame(20, $data->studentId);
        $this->assertNull($data->professionalId);
        $this->assertSame('2026-07-19 10:00:00', $data->loanDate);
        $this->assertSame('2026-07-24', $data->dueDate);
        $this->assertSame('Entrega inicial.', $data->observation);
    }
}
