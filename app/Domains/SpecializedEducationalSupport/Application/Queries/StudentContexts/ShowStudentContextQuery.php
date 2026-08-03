<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts;

use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;

final class ShowStudentContextQuery
{
    public function execute(StudentContext $context): StudentContext
    {
        return $context->load([
            'student.person',
            'student.deficiencies',
            'semester',
            'evaluator.person',
        ]);
    }
}
