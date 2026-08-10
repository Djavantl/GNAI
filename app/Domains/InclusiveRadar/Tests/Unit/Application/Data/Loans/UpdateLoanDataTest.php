<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Loans;

use App\Domains\InclusiveRadar\Application\Data\Loans\UpdateLoanData;
use Tests\TestCase;

final class UpdateLoanDataTest extends TestCase
{
    public function test_it_maps_observation(): void
    {
        $data = UpdateLoanData::from([
            'observation' => 'Observação revisada.',
        ]);

        $this->assertSame('Observação revisada.', $data->observation);
    }
}
