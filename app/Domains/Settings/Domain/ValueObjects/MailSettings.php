<?php

declare(strict_types=1);

namespace App\Domains\Settings\Domain\ValueObjects;

use App\Domains\Settings\Domain\Enums\MailEncryption;
use App\Domains\Settings\Domain\Enums\MailTransport;

final readonly class MailSettings
{
    public function __construct(
        public MailTransport $transport,
        public ?string $host,
        public ?int $port,
        public MailEncryption $encryption,
        public ?string $username,
        public ?string $password,
        public string $fromAddress,
        public string $fromName,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload, ?string $password): self
    {
        return new self(
            transport: MailTransport::tryFrom((string) ($payload['transport'] ?? '')) ?? MailTransport::SMTP,
            host: self::nullableString($payload['host'] ?? null),
            port: is_numeric($payload['port'] ?? null) ? (int) $payload['port'] : null,
            encryption: MailEncryption::tryFrom((string) ($payload['encryption'] ?? '')) ?? MailEncryption::TLS,
            username: self::nullableString($payload['username'] ?? null),
            password: $password,
            fromAddress: (string) ($payload['from_address'] ?? ''),
            fromName: (string) ($payload['from_name'] ?? ''),
        );
    }

    /**
     * Valores persistidos sem a senha, que é armazenada criptografada à parte.
     *
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'transport' => $this->transport->value,
            'host' => $this->host,
            'port' => $this->port,
            'encryption' => $this->encryption->value,
            'username' => $this->username,
            'from_address' => $this->fromAddress,
            'from_name' => $this->fromName,
        ];
    }

    /**
     * Valores que substituem os do mailer SMTP de config/mail.php.
     *
     * @return array<string, mixed>
     */
    public function smtpConfig(): array
    {
        return [
            'transport' => 'smtp',
            'scheme' => $this->encryption->scheme(),
            'url' => null,
            'host' => $this->host,
            'port' => $this->port,
            'username' => $this->username,
            'password' => $this->password,
            ...$this->encryption->transportOptions(),
        ];
    }

    private static function nullableString(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value === null || $value === '' ? null : $value;
    }
}
