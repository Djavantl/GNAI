<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\InstitutionalEvents;

use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\ListInstitutionalEventsData;
use Tests\TestCase;

final class ListInstitutionalEventsDataTest extends TestCase
{
    public function test_it_maps_snake_case_filters(): void
    {
        $data = ListInstitutionalEventsData::from([
            'title' => 'Semana',
            'is_active' => true,
            'per_page' => 20,
        ]);

        self::assertSame('Semana', $data->title);
        self::assertTrue($data->isActive);
        self::assertSame(20, $data->perPage);
    }
}
