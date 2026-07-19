<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Exceptions;

final class AssetCodeAlreadyInUse extends InclusiveRadarException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct(
            message: 'O código patrimonial já está em uso.',
            previous: $previous,
        );
    }
}
