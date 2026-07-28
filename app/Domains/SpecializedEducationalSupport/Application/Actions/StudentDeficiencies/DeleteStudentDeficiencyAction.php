<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentDeficiencies;

use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDeficiency;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteStudentDeficiencyAction
{
    /**
     * @throws Throwable
     */
    public function execute(StudentDeficiency $studentDeficiency): void
    {
        DB::transaction(function () use ($studentDeficiency): void {
            $lockedStudentDeficiency = StudentDeficiency::query()
                ->lockForUpdate()
                ->findOrFail($studentDeficiency->getKey());

            $lockedStudentDeficiency->delete();
        });
    }
}
