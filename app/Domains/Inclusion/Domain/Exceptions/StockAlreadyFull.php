<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Exceptions;

final class StockAlreadyFull extends InclusionException
{
    public function __construct()
    {
        parent::__construct(
            'Não é possível devolver uma unidade: o estoque já está completo.'
        );
    }
}
