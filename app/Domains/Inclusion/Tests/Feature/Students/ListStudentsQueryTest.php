<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Students;

use App\Domains\Inclusion\Application\Data\Students\ListStudentsData;
use App\Domains\Inclusion\Application\Queries\Students\ListStudentsQuery;
use App\Domains\Inclusion\Domain\Models\Course;
use App\Domains\Inclusion\Domain\Models\Deficiency;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentCourse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ListStudentsQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_students_by_current_course_or_deficiency_including_missing_ones(): void
    {
        $course = Course::factory()->create();
        $deficiency = Deficiency::factory()->create();
        $enrolled = Student::factory()->active()->create();
        StudentCourse::query()->create([
            'student_id' => $enrolled->id,
            'course_id' => $course->id,
            'academic_year' => 2026,
            'is_current' => true,
        ]);
        $withDeficiency = Student::factory()->active()->create();
        $withDeficiency->deficiencies()->attach($deficiency);
        $query = app(ListStudentsQuery::class);

        $fromCourse = $query->execute(ListStudentsData::from(['course_id' => (string) $course->id]));
        $withoutCourse = $query->execute(ListStudentsData::from(['course_id' => '0']));
        $fromDeficiency = $query->execute(ListStudentsData::from(['deficiency_id' => (string) $deficiency->id]));
        $withoutDeficiency = $query->execute(ListStudentsData::from(['deficiency_id' => '0']));

        self::assertSame([$enrolled->id], $fromCourse->pluck('id')->all());
        self::assertSame([$withDeficiency->id], $withoutCourse->pluck('id')->all());
        self::assertSame([$withDeficiency->id], $fromDeficiency->pluck('id')->all());
        self::assertSame([$enrolled->id], $withoutDeficiency->pluck('id')->all());
    }
}
