<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\StudentDeficiencies;

use App\Domains\Inclusion\Domain\Models\StudentDeficiency;
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
            StudentDeficiency::query()
                ->findOrFail($studentDeficiency->getKey())
                ->delete();
        });
    }
}
