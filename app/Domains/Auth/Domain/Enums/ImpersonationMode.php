<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Enums;

/**
 * Como o administrador superior navega ao entrar como outro usuário.
 */
enum ImpersonationMode: string
{
    /** Apenas consulta: qualquer requisição que altere dados é bloqueada. */
    case READ_ONLY = 'read_only';

    /** Uso completo em nome do usuário, sempre auditado com o personificador. */
    case FULL = 'full';

    public function label(): string
    {
        return match ($this) {
            self::READ_ONLY => 'Somente leitura',
            self::FULL => 'Acesso completo',
        };
    }
}
