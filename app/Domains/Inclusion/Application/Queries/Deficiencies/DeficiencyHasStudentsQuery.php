<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Deficiencies;

use App\Domains\Inclusion\Domain\Models\Deficiency;

final class DeficiencyHasStudentsQuery
{
    public function execute(Deficiency $deficiency): bool
    {
        return $deficiency->students()->exists();
    }
}
