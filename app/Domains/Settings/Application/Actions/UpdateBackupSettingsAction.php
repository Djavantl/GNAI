<?php

declare(strict_types=1);

namespace App\Domains\Settings\Application\Actions;

use App\Domains\Settings\Application\Data\UpdateBackupSettingsData;
use App\Domains\Settings\Domain\Enums\SettingCategory;
use App\Domains\Settings\Domain\Models\Setting;
use App\Domains\Settings\Domain\ValueObjects\BackupSettings;

final readonly class UpdateBackupSettingsAction
{
    public function execute(UpdateBackupSettingsData $data): BackupSettings
    {
        $settings = new BackupSettings(
            retentionMode: $data->retentionMode,
            retentionValue: $data->retentionValue,
            times: $data->times,
        );

        Setting::store(SettingCategory::BACKUP, $settings->toPayload());

        return $settings;
    }
}
