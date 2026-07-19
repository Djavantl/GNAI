<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Loans;

use App\Domains\InclusiveRadar\Application\Data\Loans\ReturnLoanData;
use Tests\TestCase;

final class ReturnLoanDataTest extends TestCase
{
    public function test_it_maps_return_payload(): void
    {
        $data = ReturnLoanData::from([
            'is_damaged' => true,
            'observation' => 'Retornou com avaria.',
        ]);

        $this->assertTrue($data->isDamaged);
        $this->assertSame('Retornou com avaria.', $data->observation);
    }

    public function test_it_defaults_to_not_damaged(): void
    {
        $data = ReturnLoanData::from([]);

        $this->assertFalse($data->isDamaged);
        $this->assertNull($data->observation);
    }
}
