<?php
namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Course;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function index(array $filters = [])
    {
        return Course::query()
            ->name($filters['name'] ?? null)
            ->active($filters['is_active'] ?? null)
            ->withCount(['disciplines', 'studentCourses']) // Útil para mostrar métricas na lista
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();
    }

    public function show(Course $course)
    {
        return $course->load('disciplines');
    }

    public function create(array $data): Course
    {
        return DB::transaction(function () use ($data) {
            $course = Course::create([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active'   => true,
            ]);

            if (!empty($data['discipline_ids'])) {
                $course->disciplines()->sync($data['discipline_ids']);
            }

            return $course;
        });
    }

    public function update(Course $course, array $data): Course
    {
        return DB::transaction(function () use ($course, $data) {
            $course->update([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active'   => $data['is_active'],
            ]);

            if (isset($data['discipline_ids'])) {
                $course->disciplines()->sync($data['discipline_ids']);
            } else {
                $course->disciplines()->sync([]);
            }

            return $course;
        });
    }

    public function delete(Course $course): void
    {
        $course->delete();
    }
}
