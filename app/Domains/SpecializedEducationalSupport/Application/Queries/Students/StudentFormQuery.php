<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Students;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;

final class StudentFormQuery
{
    /**
     * @return array<string, mixed>
     */
    public function forCreation(): array
    {
        return $this->formOptions() + [
            'defaultGender' => Gender::NOT_SPECIFIED->value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forUpdate(Student $student): array
    {
        return $this->formOptions() + [
            'student' => $student->loadMissing('person'),
            'defaultStatus' => StudentStatus::ACTIVE->value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'genders' => collect(Gender::cases())
                ->mapWithKeys(
                    static fn (Gender $gender): array => [
                        $gender->value => $gender->label(),
                    ],
                ),
            'studentStatuses' => collect(StudentStatus::cases())
                ->mapWithKeys(
                    static fn (StudentStatus $status): array => [
                        $status->value => $status->label(),
                    ],
                ),
        ];
    }
}
