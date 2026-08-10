<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Guardians;

use App\Domains\SpecializedEducationalSupport\Application\Data\Guardians\ListGuardiansData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Guardian;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListGuardiansQuery
{
    /**
     * @return LengthAwarePaginator<int, Guardian>
     */
    public function execute(Student $student, ListGuardiansData $filters): LengthAwarePaginator
    {
        $query = Guardian::query()
            ->select('student_guardians.*')
            ->join('people', 'people.id', '=', 'student_guardians.person_id')
            ->with('person')
            ->where('student_guardians.student_id', $student->getKey());

        if (filled($filters->name)) {
            $query->where('people.name', 'like', trim($filters->name).'%');
        }

        if (filled($filters->email)) {
            $query->where('people.email', 'like', '%'.trim($filters->email).'%');
        }

        if ($filters->relationship !== null) {
            $query->where('student_guardians.relationship', $filters->relationship->value);
        }

        return $query
            ->latest('student_guardians.id')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
