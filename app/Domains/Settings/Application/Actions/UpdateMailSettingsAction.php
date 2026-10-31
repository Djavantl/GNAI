<?php

declare(strict_types=1);

namespace App\Domains\Settings\Application\Actions;

use App\Domains\Settings\Application\Data\UpdateMailSettingsData;
use App\Domains\Settings\Application\Queries\GetMailSettingsQuery;
use App\Domains\Settings\Domain\Enums\SettingCategory;
use App\Domains\Settings\Domain\Models\Setting;
use App\Domains\Settings\Domain\ValueObjects\MailSettings;
use Illuminate\Support\Facades\Crypt;

final readonly class UpdateMailSettingsAction
{
    public function __construct(
        private GetMailSettingsQuery $mailSettings,
    ) {}

    public function execute(UpdateMailSettingsData $data): MailSettings
    {
        // Senha em branco mantém a atual; sem usuário, não há autenticação.
        $password = match (true) {
            $data->username === null => null,
            $data->password !== null => $data->password,
            default => $this->mailSettings->execute()?->password,
        };

        $settings = new MailSettings(
            transport: $data->transport,
            host: $data->host,
            port: $data->port,
            encryption: $data->encryption,
            username: $data->username,
            password: $password,
            fromAddress: $data->fromAddress,
            fromName: $data->fromName,
        );

        Setting::store(SettingCategory::MAIL, [
            ...$settings->toPayload(),
            'password' => $password === null ? null : Crypt::encryptString($password),
        ]);

        return $settings;
    }
}
