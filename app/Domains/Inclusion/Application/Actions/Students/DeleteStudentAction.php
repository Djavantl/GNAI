<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Students;

use App\Domains\Inclusion\Application\Queries\Students\PersonHasOtherRolesQuery;
use App\Domains\Inclusion\Application\Queries\Students\StudentHasLinkedRecordsQuery;
use App\Domains\Inclusion\Domain\Models\Person;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Infrastructure\Storage\StudentPhotoStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteStudentAction
{
    public function __construct(
        private StudentHasLinkedRecordsQuery $hasLinkedRecords,
        private PersonHasOtherRolesQuery $hasOtherRoles,
        private StudentPhotoStorage $photoStorage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Student $student): void
    {
        $photoPath = DB::transaction(function () use ($student): ?string {
            $lockedStudent = Student::query()
                ->lockForUpdate()
                ->findOrFail($student->getKey());
            $person = Person::query()
                ->lockForUpdate()
                ->findOrFail($lockedStudent->person_id);

            $lockedStudent->ensureCanBeDeleted(
                $this->hasLinkedRecords->execute($lockedStudent),
            );

            $personHasOtherRoles = $this->hasOtherRoles->execute($person, $lockedStudent);
            $lockedStudent->delete();

            if ($personHasOtherRoles) {
                return null;
            }

            $photoPath = $person->getRawOriginal('photo');
            $person->delete();

            return $photoPath;
        });

        $this->photoStorage->delete($photoPath);
    }
}
