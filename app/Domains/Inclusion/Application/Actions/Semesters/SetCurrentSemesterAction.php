<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Semesters;

use App\Domains\Inclusion\Domain\Models\Semester;
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

            Semester::markCurrentAsHistorical(except: $lockedSemester);

            $lockedSemester->markAsCurrent();
            $lockedSemester->save();

            return $lockedSemester;
        });
    }
}
