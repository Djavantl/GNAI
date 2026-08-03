<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Semesters;

use App\Domains\SpecializedEducationalSupport\Application\Data\Semesters\CreateSemesterData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Semesters\CreateSemesterDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateSemesterAction
{
    /**
     * @throws Throwable
     */
    public function execute(CreateSemesterData $data): Semester
    {
        return DB::transaction(function () use ($data): Semester {
            if ($data->isCurrent) {
                $this->markCurrentSemestersAsHistorical();
            }

            $semesterDTO = new CreateSemesterDTO(
                year: $data->year,
                term: $data->term,
                startDate: $data->startDate,
                endDate: $data->endDate,
                isCurrent: $data->isCurrent,
            );

            $semester = Semester::register($semesterDTO);
            $semester->save();

            return $semester;
        });
    }

    private function markCurrentSemestersAsHistorical(): void
    {
        Semester::query()
            ->where('is_current', true)
            ->lockForUpdate()
            ->get()
            ->each(function (Semester $semester): void {
                $semester->markAsHistorical();
                $semester->save();
            });
    }
}
