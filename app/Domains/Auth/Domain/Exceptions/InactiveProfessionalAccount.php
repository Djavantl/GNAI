<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Exceptions;

use DomainException;

final class InactiveProfessionalAccount extends DomainException
{
    public static function make(): self
    {
        return new self('Este profissional está inativo e não pode acessar o sistema.');
    }
}
