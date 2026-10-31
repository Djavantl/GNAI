<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Application\Data\Loans;

use App\Domains\Inclusion\Application\Data\Loans\ListLoansData;
use App\Domains\Inclusion\Domain\Enums\LoanStatus;
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

        self::assertSame('Ana', $data->student);
        self::assertSame('Marley', $data->professional);
        self::assertSame('Linha Braille', $data->item);
        self::assertSame(LoanStatus::ACTIVE, $data->status);
        self::assertSame(25, $data->perPage);
    }

    public function test_it_applies_default_filters(): void
    {
        $data = ListLoansData::from([]);

        self::assertNull($data->student);
        self::assertNull($data->professional);
        self::assertNull($data->item);
        self::assertNull($data->status);
        self::assertSame(10, $data->perPage);
    }
}
