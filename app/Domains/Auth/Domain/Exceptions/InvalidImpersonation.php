<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Exceptions;

use App\Shared\Application\Exceptions\AccessDeniedException;

final class InvalidImpersonation extends AccessDeniedException
{
    public static function impersonatorMustBeSuperAdmin(): self
    {
        return new self('Apenas o administrador superior do Gnai pode entrar como outro usuário.');
    }

    public static function cannotImpersonateSuperAdmin(): self
    {
        return new self('Não é possível entrar como outro administrador superior.');
    }

    public static function targetWithoutAccess(): self
    {
        return new self('Este usuário não tem acesso ao sistema.');
    }

    public static function cannotImpersonateSelf(): self
    {
        return new self('Não é possível entrar como você mesmo.');
    }

    public static function notActive(): self
    {
        return new self('Você não está navegando como outro usuário.');
    }

    public static function invalidImpersonator(): self
    {
        return new self('Administrador original inválido ou sem permissão.');
    }
}
