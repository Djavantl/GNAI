<?php

declare(strict_types=1);

namespace App\Domains\Backup\Tests\Unit\Application\Data;

use App\Domains\Backup\Application\Data\ListBackupsData;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use Tests\TestCase;

final class ListBackupsDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = ListBackupsData::from([
            'name' => 'backup',
            'status' => 'success',
            'user_id' => 5,
            'per_page' => 25,
        ]);

        self::assertSame('backup', $data->name);
        self::assertSame(BackupStatus::SUCCESS, $data->status);
        self::assertSame(5, $data->userId);
        self::assertSame(25, $data->perPage);
    }

    public function test_it_applies_defaults(): void
    {
        $data = ListBackupsData::from([]);

        self::assertNull($data->name);
        self::assertNull($data->status);
        self::assertNull($data->userId);
        self::assertSame(10, $data->perPage);
    }
}
