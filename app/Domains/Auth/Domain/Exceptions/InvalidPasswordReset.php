<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Exceptions;

use App\Exceptions\BusinessRuleException;

final class InvalidPasswordReset extends BusinessRuleException
{
    public static function invalidToken(): self
    {
        return new self('Token inválido ou expirado.');
    }
}
