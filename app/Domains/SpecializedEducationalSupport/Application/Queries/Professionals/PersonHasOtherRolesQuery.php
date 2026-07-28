<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Professionals;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;

final class PersonHasOtherRolesQuery
{
    public function execute(Person $person, Professional $professional): bool
    {
        return $person->student()->exists()
            || $person->teacher()->exists()
            || $person->guardians()->exists()
            || Professional::query()
                ->where('person_id', $person->getKey())
                ->whereKeyNot($professional->getKey())
                ->exists();
    }
}
