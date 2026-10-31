<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Peis;

use App\Domains\Inclusion\Domain\Models\Discipline;
use App\Domains\Inclusion\Domain\Models\Semester;
use App\Domains\Inclusion\Domain\Models\Student;
use Illuminate\Database\Eloquent\Collection;

final class PeiFilterOptionsQuery
{
    /**
     * @return Collection<int, Student>
     */
    public function students(): Collection
    {
        return Student::query()
            ->with('person')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, Semester>
     */
    public function semesters(): Collection
    {
        return Semester::query()
            ->orderByDesc('year')
            ->orderByDesc('term')
            ->get(['id', 'label']);
    }

    /**
     * @return Collection<int, Discipline>
     */
    public function disciplines(): Collection
    {
        return Discipline::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
