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
use Illuminate\Support\Str;
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
            $detail = $this->sanitize(
                $exception->getMessage(),
                $settings?->password ?? $this->config->get('mail.mailers.smtp.password'),
            );

            Log::warning('Falha ao enviar o e-mail de teste das configurações.', [
                'recipient' => $data->recipient,
                'error' => $detail,
            ]);

            throw new MailTestFailed($this->describe($detail));
        }

        return new TestMailResultDTO(
            usedStoredSettings: $settings !== null,
            loggedOnly: $this->config->get('mail.default') === 'log',
        );
    }

    private function sanitize(string $message, mixed $secret): string
    {
        if (is_string($secret) && $secret !== '') {
            $message = str_replace($secret, '********', $message);
        }

        return trim((string) preg_replace('/\s+/', ' ', $message));
    }

    /**
     * Mensagem curta para a interface; o detalhe técnico completo fica no log.
     */
    private function describe(string $detail): string
    {
        $normalized = mb_strtolower($detail);
        $matches = static fn (array $needles): bool => Str::contains($normalized, $needles);

        return 'E-mail de teste não enviado: '.match (true) {
            $matches(['authenticat', '"535', ' 535', 'username and password']) => 'o servidor recusou o usuário ou a senha. Confira o usuário e, no Gmail ou Microsoft, gere uma nova senha de app.',
            $matches(['getaddrinfo', 'name or service not known', 'connection refused', 'timed out', 'network is unreachable', 'no route to host']) => 'não foi possível conectar ao servidor SMTP. Confira o servidor e a porta.',
            $matches(['ssl operation failed', 'starttls', 'certificate', 'crypto', 'wrong version number', 'tls']) => 'falha na conexão segura. Confira a segurança e a porta (587 com STARTTLS ou 465 com SSL/TLS).',
            $matches(['connection could not be established']) => 'não foi possível conectar ao servidor SMTP. Confira o servidor, a porta e a segurança.',
            $matches(['"550', '"553', '"554', ' 550', ' 553', ' 554', 'sender', 'relay']) => 'o servidor recusou o remetente ou o destinatário. Confira o e-mail de envio e o destino.',
            default => 'confira as configurações de e-mail. Detalhe: '.Str::limit($detail, 120),
        };
    }
}
