<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Courses;

use App\Domains\SpecializedEducationalSupport\Application\Data\Courses\ListCoursesData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListCoursesQuery
{
    /**
     * @return LengthAwarePaginator<int, Course>
     */
    public function execute(ListCoursesData $filters): LengthAwarePaginator
    {
        $query = Course::query()
            ->withCount(['disciplines', 'studentCourses']);

        if (filled($filters->name)) {
            $query->where('name', 'like', '%'.trim($filters->name).'%');
        }

        if ($filters->isActive !== null) {
            $query->where('is_active', $filters->isActive);
        }

        return $query
            ->orderBy('name')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
