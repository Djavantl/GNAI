<?php

declare(strict_types=1);

namespace App\Domains\Settings\Application\Queries;

use App\Domains\Settings\Domain\Enums\BackupRetentionMode;
use App\Domains\Settings\Domain\Enums\MailEncryption;
use App\Domains\Settings\Domain\Enums\MailTransport;
use App\Domains\Settings\Domain\ValueObjects\BackupSettings;
use App\Domains\Settings\Domain\ValueObjects\MailSettings;
use Illuminate\Support\Collection;

final readonly class SettingsFormQuery
{
    public function __construct(
        private GetMailSettingsQuery $mailSettings,
        private GetBackupSettingsQuery $backupSettings,
    ) {}

    /**
     * A senha nunca é enviada à interface: apenas se existe uma cadastrada.
     *
     * @return array<string, mixed>
     */
    public function forMail(): array
    {
        $settings = $this->mailSettings->execute();
        $configured = $settings instanceof MailSettings;
        $settings ??= $this->environmentMailSettings();

        return [
            'configured' => $configured,
            'mail' => [
                ...$settings->toPayload(),
                'has_password' => $configured && filled($settings->password),
            ],
            'transports' => $this->options(MailTransport::cases()),
            'encryptions' => $this->options(MailEncryption::cases()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forBackup(): array
    {
        return [
            'backup' => $this->backupSettings->execute()->toPayload(),
            'defaults' => BackupSettings::defaults()->toPayload(),
            'retentionModes' => $this->options(BackupRetentionMode::cases()),
            'timezone' => (string) config('app.timezone'),
        ];
    }

    /**
     * Valores atuais do .env, usados apenas para pré-preencher o primeiro cadastro.
     */
    private function environmentMailSettings(): MailSettings
    {
        $smtp = (array) config('mail.mailers.smtp', []);
        $port = is_numeric($smtp['port'] ?? null) ? (int) $smtp['port'] : null;

        return MailSettings::fromPayload([
            'transport' => config('mail.default'),
            'host' => $smtp['host'] ?? null,
            'port' => $port,
            'encryption' => ($smtp['scheme'] ?? null) === 'smtps' || $port === 465
                ? MailEncryption::SSL->value
                : MailEncryption::TLS->value,
            'username' => $smtp['username'] ?? null,
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ], null);
    }

    /**
     * @param  list<MailTransport|MailEncryption|BackupRetentionMode>  $cases
     * @return Collection<string, string>
     */
    private function options(array $cases): Collection
    {
        return collect($cases)->mapWithKeys(
            static fn (MailTransport|MailEncryption|BackupRetentionMode $case): array => [
                $case->value => $case->label(),
            ],
        );
    }
}
