<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Students;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;

final class PersonHasOtherRolesQuery
{
    public function execute(Person $person, Student $student): bool
    {
        return Student::query()
            ->where('person_id', $person->getKey())
            ->whereKeyNot($student->getKey())
            ->exists()
            || $person->professional()->exists()
            || $person->teacher()->exists()
            || $person->guardians()->exists();
    }
}
