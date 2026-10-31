<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Semesters;

use App\Domains\Inclusion\Application\Data\Semesters\CreateSemesterData;
use App\Domains\Inclusion\Domain\DTOs\Semesters\CreateSemesterDTO;
use App\Domains\Inclusion\Domain\Models\Semester;
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
                Semester::markCurrentAsHistorical();
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
}
