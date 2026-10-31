<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Exceptions;

final class InsufficientStock extends InclusionException
{
    public function __construct()
    {
        parent::__construct(
            'Não há unidades disponíveis em estoque.'
        );
    }
}
