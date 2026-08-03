<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Disciplines;

use App\Domains\SpecializedEducationalSupport\Application\Queries\Disciplines\DisciplineHasCoursesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Disciplines\DisciplineHasTeachersQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteDisciplineAction
{
    public function __construct(
        private DisciplineHasTeachersQuery $hasTeachers,
        private DisciplineHasCoursesQuery $hasCourses,
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
            );

            $lockedDiscipline->delete();
        });
    }
}
