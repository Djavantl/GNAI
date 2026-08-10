<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Exceptions;

final class ResourceHasOpenLoans extends InclusiveRadarException
{
    public function __construct()
    {
        parent::__construct(
            'Não é possível excluir um item com empréstimos ativos.'
        );
    }
}
