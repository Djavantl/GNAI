<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentDeficiencies;

use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDeficiency;

final class ShowStudentDeficiencyQuery
{
    public function execute(StudentDeficiency $studentDeficiency): StudentDeficiency
    {
        return $studentDeficiency->load('deficiency');
    }
}
