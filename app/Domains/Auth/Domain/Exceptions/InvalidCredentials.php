<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Exceptions;

use App\Exceptions\BusinessRuleException;

final class InvalidCredentials extends BusinessRuleException
{
    public static function make(): self
    {
        return new self('Credenciais inválidas.');
    }
}
