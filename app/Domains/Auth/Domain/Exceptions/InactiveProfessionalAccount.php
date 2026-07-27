<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Exceptions;

use App\Exceptions\AccessDeniedException;

final class InactiveProfessionalAccount extends AccessDeniedException
{
    public static function make(): self
    {
        return new self('Este profissional está inativo e não pode acessar o sistema.');
    }
}
