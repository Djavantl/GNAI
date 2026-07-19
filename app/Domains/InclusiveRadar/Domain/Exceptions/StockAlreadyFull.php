<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Exceptions;

final class StockAlreadyFull extends InclusiveRadarException
{
    public function __construct()
    {
        parent::__construct(
            'Não é possível devolver uma unidade: o estoque já está completo.'
        );
    }
}
