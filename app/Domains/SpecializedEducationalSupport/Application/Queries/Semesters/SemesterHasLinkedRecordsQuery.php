<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Semesters;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;

final class SemesterHasLinkedRecordsQuery
{
    public function execute(Semester $semester): bool
    {
        return $semester->studentContexts()->exists()
            || $semester->studentDocuments()->exists()
            || $semester->peis()->exists();
    }
}
