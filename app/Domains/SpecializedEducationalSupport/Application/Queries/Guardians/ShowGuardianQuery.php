<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Guardians;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Guardian;

final class ShowGuardianQuery
{
    public function execute(Guardian $guardian): Guardian
    {
        return $guardian->load([
            'person',
            'student.person',
        ]);
    }
}
