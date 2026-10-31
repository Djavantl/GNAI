<?php

declare(strict_types=1);

namespace App\Domains\Settings\Domain\Enums;

enum MailEncryption: string
{
    case TLS = 'tls';
    case SSL = 'ssl';
    case NONE = 'none';

    public function label(): string
    {
        return match ($this) {
            self::TLS => 'STARTTLS (geralmente porta 587)',
            self::SSL => 'SSL/TLS (geralmente porta 465)',
            self::NONE => 'Sem criptografia',
        };
    }

    public function scheme(): string
    {
        return $this === self::SSL ? 'smtps' : 'smtp';
    }

    /**
     * Opções repassadas ao transporte SMTP do Symfony Mailer.
     *
     * @return array{auto_tls: bool, require_tls: bool}
     */
    public function transportOptions(): array
    {
        return match ($this) {
            self::TLS => ['auto_tls' => true, 'require_tls' => true],
            self::SSL => ['auto_tls' => true, 'require_tls' => false],
            self::NONE => ['auto_tls' => false, 'require_tls' => false],
        };
    }
}
