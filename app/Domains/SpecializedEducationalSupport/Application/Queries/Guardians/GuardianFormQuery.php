<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Guardians;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\GuardianRelationship;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Guardian;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;

final class GuardianFormQuery
{
    public function forCreation(Student $student): array
    {
        $student->ensureIsActive();

        return $this->formOptions() + [
            'student' => $student->loadMissing('person'),
            'defaultGender' => Gender::NOT_SPECIFIED->value,
        ];
    }

    public function forUpdate(Student $student, Guardian $guardian): array
    {
        $guardian->ensureBelongsTo($student);
        $student->ensureIsActive();

        return $this->formOptions() + [
            'student' => $student->loadMissing('person'),
            'guardian' => $guardian->loadMissing('person'),
        ];
    }

    public function relationshipOptions(): array
    {
        return $this->formOptions()['relationships']->all();
    }

    private function formOptions(): array
    {
        return [
            'genders' => collect(Gender::cases())
                ->mapWithKeys(
                    static fn (Gender $gender): array => [
                        $gender->value => $gender->label(),
                    ],
                ),
            'relationships' => collect(GuardianRelationship::cases())
                ->mapWithKeys(
                    static fn (GuardianRelationship $relationship): array => [
                        $relationship->value => $relationship->label(),
                    ],
                ),
        ];
    }
}
