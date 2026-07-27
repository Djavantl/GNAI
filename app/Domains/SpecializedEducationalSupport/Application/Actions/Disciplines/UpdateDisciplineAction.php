<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Disciplines;

use App\Domains\SpecializedEducationalSupport\Application\Data\Disciplines\UpdateDisciplineData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Disciplines\DisciplineHasCoursesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Disciplines\DisciplineHasTeachersQuery;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Disciplines\UpdateDisciplineDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateDisciplineAction
{
    public function __construct(
        private DisciplineHasCoursesQuery $hasCourses,
        private DisciplineHasTeachersQuery $hasTeachers,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Discipline $discipline, UpdateDisciplineData $data): Discipline
    {
        return DB::transaction(function () use ($discipline, $data): Discipline {
            $lockedDiscipline = Discipline::query()
                ->lockForUpdate()
                ->findOrFail($discipline->getKey());

            if ($lockedDiscipline->is_active && ! $data->isActive) {
                $lockedDiscipline->ensureCanBeDeactivated(
                    hasCourses: $this->hasCourses->execute($lockedDiscipline),
                    hasTeachers: $this->hasTeachers->execute($lockedDiscipline),
                );
            }

            $disciplineDTO = new UpdateDisciplineDTO(
                name: $data->name,
                description: $data->description,
                isActive: $data->isActive,
            );

            $lockedDiscipline->revise($disciplineDTO);
            $lockedDiscipline->save();

            return $lockedDiscipline;
        });
    }
}
