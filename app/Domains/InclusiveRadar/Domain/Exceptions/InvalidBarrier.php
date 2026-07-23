<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Exceptions;

final class InvalidBarrier extends InclusiveRadarException
{
    public function __construct(string $message = 'A barreira informada é inválida.')
    {
        parent::__construct($message);
    }
}
