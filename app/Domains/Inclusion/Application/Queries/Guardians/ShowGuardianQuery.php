<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Guardians;

use App\Domains\Inclusion\Domain\Models\Guardian;

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
