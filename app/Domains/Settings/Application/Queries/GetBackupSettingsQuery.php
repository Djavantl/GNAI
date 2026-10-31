<?php

declare(strict_types=1);

namespace App\Domains\Settings\Application\Queries;

use App\Domains\Settings\Domain\Enums\SettingCategory;
use App\Domains\Settings\Domain\Models\Setting;
use App\Domains\Settings\Domain\ValueObjects\BackupSettings;

final readonly class GetBackupSettingsQuery
{
    public function execute(): BackupSettings
    {
        $payload = Setting::payloadFor(SettingCategory::BACKUP);

        return $payload === null
            ? BackupSettings::defaults()
            : BackupSettings::fromPayload($payload);
    }
}
