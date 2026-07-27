<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Deficiencies;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;

final class DeficiencyHasStudentsQuery
{
    public function execute(Deficiency $deficiency): bool
    {
        return $deficiency->students()->exists();
    }
}
