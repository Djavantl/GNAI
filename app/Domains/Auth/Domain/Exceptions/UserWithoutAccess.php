<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Exceptions;

use App\Exceptions\AccessDeniedException;

final class UserWithoutAccess extends AccessDeniedException
{
    public static function make(): self
    {
        return new self('Usuário sem permissão de acesso.');
    }
}
