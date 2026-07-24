<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Exceptions;

use DomainException;

final class InvalidCredentials extends DomainException
{
    public static function make(): self
    {
        return new self('Credenciais inválidas.');
    }
}
