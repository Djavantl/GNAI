<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Application\Data\Loans;

use App\Domains\Inclusion\Application\Data\Loans\CreateLoanData;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use Tests\TestCase;

final class CreateLoanDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = CreateLoanData::from([
            'loanable_id' => 10,
            'item_type' => ItemType::AssistiveTechnology->value,
            'student_id' => 20,
            'loan_date' => '2026-07-19 10:00:00',
            'due_date' => '2026-07-24',
            'observation' => 'Entrega inicial.',
        ]);

        self::assertSame(10, $data->loanableId);
        self::assertSame(ItemType::AssistiveTechnology, $data->itemType);
        self::assertSame(20, $data->studentId);
        self::assertNull($data->professionalId);
        self::assertSame('2026-07-19 10:00:00', $data->loanDate);
        self::assertSame('2026-07-24', $data->dueDate);
        self::assertSame('Entrega inicial.', $data->observation);
    }
}
