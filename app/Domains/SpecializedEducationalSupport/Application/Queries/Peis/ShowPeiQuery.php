<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Peis;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;

final class ShowPeiQuery
{
    public function execute(Pei $pei): Pei
    {
        return $pei->load([
            'student.person',
            'student.deficiencies',
            'studentContext',
            'course',
            'semester',
            'creator',
        ]);
    }
}
