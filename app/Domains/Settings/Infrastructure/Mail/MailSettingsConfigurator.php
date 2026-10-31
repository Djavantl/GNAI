<?php

declare(strict_types=1);

namespace App\Domains\Settings\Infrastructure\Mail;

use App\Domains\Settings\Application\Queries\GetMailSettingsQuery;
use App\Domains\Settings\Domain\Enums\MailTransport;
use App\Domains\Settings\Domain\ValueObjects\MailSettings;
use Illuminate\Contracts\Config\Repository;

/**
 * Aplica as configurações de e-mail cadastradas sobre config/mail.php. Sem
 * cadastro, mantém os valores vindos do .env.
 */
final readonly class MailSettingsConfigurator
{
    public function __construct(
        private Repository $config,
        private GetMailSettingsQuery $mailSettings,
    ) {}

    public function apply(): ?MailSettings
    {
        $settings = $this->mailSettings->execute();

        if (! $settings instanceof MailSettings) {
            return null;
        }

        $this->config->set('mail.default', $settings->transport->value);
        $this->config->set('mail.from', [
            'address' => $settings->fromAddress,
            'name' => $settings->fromName,
        ]);

        if ($settings->transport === MailTransport::SMTP) {
            $this->config->set('mail.mailers.smtp', [
                ...(array) $this->config->get('mail.mailers.smtp', []),
                ...$settings->smtpConfig(),
            ]);
        }

        return $settings;
    }
}
