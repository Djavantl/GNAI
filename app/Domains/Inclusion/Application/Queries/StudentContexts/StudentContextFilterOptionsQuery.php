<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\StudentContexts;

use App\Domains\Inclusion\Domain\Models\Semester;

final class StudentContextFilterOptionsQuery
{
    public function execute(): array
    {
        return [
            'semesters' => Semester::query()
                ->orderByDesc('year')
                ->orderByDesc('term')
                ->pluck('label', 'id')
                ->prepend('Semestre (Todos)', ''),
        ];
    }
}
