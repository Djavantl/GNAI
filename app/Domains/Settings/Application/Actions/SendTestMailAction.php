<?php

declare(strict_types=1);

namespace App\Domains\Settings\Application\Actions;

use App\Domains\Settings\Application\Data\SendTestMailData;
use App\Domains\Settings\Domain\DTOs\TestMailResultDTO;
use App\Domains\Settings\Domain\Exceptions\MailTestFailed;
use App\Domains\Settings\Infrastructure\Mail\MailSettingsConfigurator;
use App\Domains\Settings\Infrastructure\Mail\SettingsTestMail;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class SendTestMailAction
{
    public function __construct(
        private MailSettingsConfigurator $configurator,
        private MailManager $mail,
        private Repository $config,
    ) {}

    /**
     * Envia usando a configuração efetiva: a cadastrada ou, sem cadastro, a do .env.
     *
     * @throws MailTestFailed
     */
    public function execute(SendTestMailData $data): TestMailResultDTO
    {
        $settings = $this->configurator->apply();
        $this->mail->forgetMailers();

        try {
            $this->mail->mailer()->to($data->recipient)->send(new SettingsTestMail);
        } catch (Throwable $exception) {
            $detail = $exception->getMessage();
            $password = $settings?->password ?? $this->config->get('mail.mailers.smtp.password');

            if (is_string($password) && $password !== '') {
                $detail = str_replace($password, '********', $detail);
            }

            $detail = trim((string) preg_replace('/\s+/', ' ', $detail));

            Log::warning('Falha ao enviar o e-mail de teste das configurações.', [
                'recipient' => $data->recipient,
                'error' => $detail,
            ]);

            throw MailTestFailed::fromDetail($detail);
        }

        return new TestMailResultDTO(
            usedStoredSettings: $settings !== null,
            loggedOnly: $this->config->get('mail.default') === 'log',
        );
    }
}
