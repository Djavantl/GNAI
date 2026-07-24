<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Exceptions;

use DomainException;

final class InvalidPasswordReset extends DomainException
{
    public static function invalidToken(): self
    {
        return new self('Token inválido ou expirado.');
    }
}
