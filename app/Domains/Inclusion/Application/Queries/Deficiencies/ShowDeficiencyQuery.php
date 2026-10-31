<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Deficiencies;

use App\Domains\Inclusion\Domain\Models\Deficiency;

final class ShowDeficiencyQuery
{
    public function execute(Deficiency $deficiency): Deficiency
    {
        return $deficiency;
    }
}
