<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Application\Data\Loans;

use App\Domains\Inclusion\Application\Data\Loans\ReturnLoanData;
use Tests\TestCase;

final class ReturnLoanDataTest extends TestCase
{
    public function test_it_maps_return_payload(): void
    {
        $data = ReturnLoanData::from([
            'is_damaged' => true,
            'observation' => 'Retornou com avaria.',
        ]);

        self::assertTrue($data->isDamaged);
        self::assertSame('Retornou com avaria.', $data->observation);
    }

    public function test_it_defaults_to_not_damaged(): void
    {
        $data = ReturnLoanData::from([]);

        self::assertFalse($data->isDamaged);
        self::assertNull($data->observation);
    }
}
