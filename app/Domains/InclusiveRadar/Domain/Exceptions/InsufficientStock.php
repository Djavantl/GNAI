<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Exceptions;

final class InsufficientStock extends InclusiveRadarException
{
    public function __construct()
    {
        parent::__construct(
            'Não há unidades disponíveis em estoque.'
        );
    }
}
