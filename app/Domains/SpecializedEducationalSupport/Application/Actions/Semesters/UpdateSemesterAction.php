<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Semesters;

use App\Domains\SpecializedEducationalSupport\Application\Data\Semesters\UpdateSemesterData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Semesters\UpdateSemesterDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateSemesterAction
{
    /**
     * @throws Throwable
     */
    public function execute(Semester $semester, UpdateSemesterData $data): Semester
    {
        return DB::transaction(function () use ($semester, $data): Semester {
            $lockedSemester = Semester::query()
                ->lockForUpdate()
                ->findOrFail($semester->getKey());

            if ($data->isCurrent) {
                $this->markOtherCurrentSemestersAsHistorical($lockedSemester);
            }

            $semesterDTO = new UpdateSemesterDTO(
                year: $data->year,
                term: $data->term,
                startDate: $data->startDate,
                endDate: $data->endDate,
                isCurrent: $data->isCurrent,
            );

            $lockedSemester->revise($semesterDTO);
            $lockedSemester->save();

            return $lockedSemester;
        });
    }

    private function markOtherCurrentSemestersAsHistorical(Semester $currentSemester): void
    {
        Semester::query()
            ->where('is_current', true)
            ->whereKeyNot($currentSemester->getKey())
            ->lockForUpdate()
            ->get()
            ->each(function (Semester $semester): void {
                $semester->markAsHistorical();
                $semester->save();
            });
    }
}
