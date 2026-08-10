<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Students;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;

final class StudentFilterOptionsQuery
{
    public function execute(): array
    {
        return [
            'courses' => Course::query()->orderBy('name')->get(['id', 'name']),
            'deficiencies' => Deficiency::query()->orderBy('name')->get(['id', 'name']),
        ];
    }
}
