<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Waitlists;

use App\Domains\InclusiveRadar\Application\Data\Waitlists\CreateWaitlistData;
use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use Tests\TestCase;

final class CreateWaitlistDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = CreateWaitlistData::from([
            'waitlistable_id' => 10,
            'waitlistable_type' => LoanableType::AssistiveTechnology->value,
            'student_id' => 20,
            'professional_id' => null,
            'observation' => 'Aguardar disponibilidade.',
        ]);

        self::assertSame(10, $data->waitlistableId);
        self::assertSame(LoanableType::AssistiveTechnology, $data->waitlistableType);
        self::assertSame(20, $data->studentId);
        self::assertNull($data->professionalId);
        self::assertSame('Aguardar disponibilidade.', $data->observation);
    }
}
