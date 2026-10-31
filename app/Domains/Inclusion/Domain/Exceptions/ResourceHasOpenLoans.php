<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Exceptions;

final class ResourceHasOpenLoans extends InclusionException
{
    public function __construct()
    {
        parent::__construct(
            'Não é possível excluir um item com empréstimos ativos.'
        );
    }
}
