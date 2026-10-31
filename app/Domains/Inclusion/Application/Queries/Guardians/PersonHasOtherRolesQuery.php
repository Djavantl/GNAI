<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Guardians;

use App\Domains\Inclusion\Domain\Models\Guardian;
use App\Domains\Inclusion\Domain\Models\Person;

final class PersonHasOtherRolesQuery
{
    public function execute(Person $person, Guardian $guardian): bool
    {
        return $person->student()->exists()
            || $person->professional()->exists()
            || $person->teacher()->exists()
            || Guardian::query()
                ->where('person_id', $person->getKey())
                ->whereKeyNot($guardian->getKey())
                ->exists();
    }
}
