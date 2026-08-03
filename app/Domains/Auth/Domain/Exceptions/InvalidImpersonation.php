<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Exceptions;

use App\Shared\Application\Exceptions\AccessDeniedException;

final class InvalidImpersonation extends AccessDeniedException
{
    public static function impersonatorMustBeAdmin(): self
    {
        return new self('Apenas administradores podem impersonar usuários.');
    }

    public static function cannotImpersonateAdmin(): self
    {
        return new self('Não é possível impersonar outro administrador.');
    }

    public static function cannotImpersonateSelf(): self
    {
        return new self('Não é possível impersonar você mesmo.');
    }

    public static function notActive(): self
    {
        return new self('Você não está em uma impersonação.');
    }

    public static function invalidImpersonator(): self
    {
        return new self('Administrador original inválido ou sem permissão.');
    }
}
