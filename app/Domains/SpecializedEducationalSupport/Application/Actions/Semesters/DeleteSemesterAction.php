<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Semesters;

use App\Domains\SpecializedEducationalSupport\Application\Queries\Semesters\SemesterHasLinkedRecordsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteSemesterAction
{
    public function __construct(
        private SemesterHasLinkedRecordsQuery $hasLinkedRecords,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Semester $semester): void
    {
        DB::transaction(function () use ($semester): void {
            $lockedSemester = Semester::query()
                ->lockForUpdate()
                ->findOrFail($semester->getKey());

            $lockedSemester->ensureCanBeDeleted(
                $this->hasLinkedRecords->execute($lockedSemester),
            );

            $lockedSemester->delete();
        });
    }
}
