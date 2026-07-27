<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Semesters;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class SetCurrentSemesterAction
{
    /**
     * @throws Throwable
     */
    public function execute(Semester $semester): Semester
    {
        return DB::transaction(function () use ($semester): Semester {
            $lockedSemester = Semester::query()
                ->lockForUpdate()
                ->findOrFail($semester->getKey());

            Semester::query()
                ->where('is_current', true)
                ->whereKeyNot($lockedSemester->getKey())
                ->lockForUpdate()
                ->get()
                ->each(function (Semester $currentSemester): void {
                    $currentSemester->markAsHistorical();
                    $currentSemester->save();
                });

            $lockedSemester->markAsCurrent();
            $lockedSemester->save();

            return $lockedSemester;
        });
    }
}
