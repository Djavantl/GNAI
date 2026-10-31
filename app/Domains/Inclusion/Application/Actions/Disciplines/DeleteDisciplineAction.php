<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Disciplines;

use App\Domains\Inclusion\Application\Queries\Disciplines\DisciplineHasCoursesQuery;
use App\Domains\Inclusion\Application\Queries\Disciplines\DisciplineHasStudentCourseRecordsQuery;
use App\Domains\Inclusion\Application\Queries\Disciplines\DisciplineHasTeachersQuery;
use App\Domains\Inclusion\Domain\Models\Discipline;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteDisciplineAction
{
    public function __construct(
        private DisciplineHasTeachersQuery $hasTeachers,
        private DisciplineHasCoursesQuery $hasCourses,
        private DisciplineHasStudentCourseRecordsQuery $hasStudentCourseRecords,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Discipline $discipline): void
    {
        DB::transaction(function () use ($discipline): void {
            $lockedDiscipline = Discipline::query()
                ->lockForUpdate()
                ->findOrFail($discipline->getKey());

            $lockedDiscipline->ensureCanBeDeleted(
                hasTeachers: $this->hasTeachers->execute($lockedDiscipline),
                hasCourses: $this->hasCourses->execute($lockedDiscipline),
                hasStudentCourseRecords: $this->hasStudentCourseRecords->execute($lockedDiscipline),
            );

            $lockedDiscipline->delete();
        });
    }
}
