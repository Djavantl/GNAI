<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Loans;

use App\Domains\InclusiveRadar\Application\Data\Loans\ListLoansData;
use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use Tests\TestCase;

final class ListLoansDataTest extends TestCase
{
    public function test_it_maps_filters_and_status_to_domain_types(): void
    {
        $data = ListLoansData::from([
            'student' => 'Ana',
            'professional' => 'Marley',
            'item' => 'Linha Braille',
            'status' => LoanStatus::ACTIVE->value,
            'per_page' => 25,
        ]);

        $this->assertSame('Ana', $data->student);
        $this->assertSame('Marley', $data->professional);
        $this->assertSame('Linha Braille', $data->item);
        $this->assertSame(LoanStatus::ACTIVE, $data->status);
        $this->assertSame(25, $data->perPage);
    }

    public function test_it_applies_default_filters(): void
    {
        $data = ListLoansData::from([]);

        $this->assertNull($data->student);
        $this->assertNull($data->professional);
        $this->assertNull($data->item);
        $this->assertNull($data->status);
        $this->assertSame(10, $data->perPage);
    }
}
