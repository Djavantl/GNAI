<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Waitlists;

use App\Domains\InclusiveRadar\Application\Data\Waitlists\UpdateWaitlistData;
use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use Tests\TestCase;

final class UpdateWaitlistDataTest extends TestCase
{
    public function test_it_maps_status_and_observation(): void
    {
        $data = UpdateWaitlistData::from([
            'status' => WaitlistStatus::NOTIFIED->value,
            'observation' => 'Beneficiário notificado.',
        ]);

        self::assertSame(WaitlistStatus::NOTIFIED, $data->status);
        self::assertSame('Beneficiário notificado.', $data->observation);
    }

    public function test_it_applies_default_values(): void
    {
        $data = UpdateWaitlistData::from([]);

        self::assertNull($data->status);
        self::assertNull($data->observation);
    }
}
