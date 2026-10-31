<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Teachers;

use App\Domains\Inclusion\Application\Data\Teachers\UpdateTeacherDisciplinesData;
use App\Domains\Inclusion\Domain\Exceptions\InvalidTeacher;
use App\Domains\Inclusion\Domain\Models\Teacher;
use DomainException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateTeacherDisciplinesAction
{
    /**
     * @throws InvalidTeacher
     * @throws Throwable
     */
    public function execute(Teacher $teacher, UpdateTeacherDisciplinesData $data): void
    {
        try {
            DB::transaction(function () use ($teacher, $data): void {
                $lockedTeacher = Teacher::query()
                    ->lockForUpdate()
                    ->findOrFail($teacher->getKey());

                $lockedTeacher->assignCourseDisciplines($data->assignments);
            });
        } catch (DomainException $exception) {
            throw new InvalidTeacher($exception->getMessage(), previous: $exception);
        }
    }
}
