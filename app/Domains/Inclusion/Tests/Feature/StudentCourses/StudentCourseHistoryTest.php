<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\StudentCourses;

use App\Domains\Inclusion\Application\Actions\StudentCourses\CreateStudentCourseAction;
use App\Domains\Inclusion\Application\Actions\StudentCourses\DeleteStudentCourseAction;
use App\Domains\Inclusion\Application\Actions\StudentCourses\SetCurrentStudentCourseAction;
use App\Domains\Inclusion\Application\Data\StudentCourses\CreateStudentCourseData;
use App\Domains\Inclusion\Application\Data\StudentCourses\ListStudentCoursesData;
use App\Domains\Inclusion\Application\Queries\StudentCourses\ListStudentCoursesQuery;
use App\Domains\Inclusion\Domain\Models\Course;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentCourse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StudentCourseHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_the_current_and_most_recent_courses_first(): void
    {
        [$student, $oldest, $previous, $current] = $this->studentWithHistory();

        $studentCourses = app(ListStudentCoursesQuery::class)->execute(
            $student,
            new ListStudentCoursesData(perPage: 10),
        );

        self::assertSame([$current->id, $previous->id, $oldest->id], $studentCourses->pluck('id')->all());
    }

    public function test_it_promotes_the_most_recent_previous_course_when_deleting_the_current_one(): void
    {
        [, $oldest, $previous, $current] = $this->studentWithHistory();

        app(DeleteStudentCourseAction::class)->execute($current);

        $this->assertDatabaseMissing('student_courses', ['id' => $current->id]);
        $this->assertDatabaseHas('student_courses', ['id' => $previous->id, 'is_current' => true]);
        $this->assertDatabaseHas('student_courses', ['id' => $oldest->id, 'is_current' => false]);
    }

    public function test_it_keeps_the_current_course_when_deleting_a_historical_one(): void
    {
        [, $oldest, $previous, $current] = $this->studentWithHistory();

        app(DeleteStudentCourseAction::class)->execute($previous);

        $this->assertDatabaseMissing('student_courses', ['id' => $previous->id]);
        $this->assertDatabaseHas('student_courses', ['id' => $current->id, 'is_current' => true]);
        $this->assertDatabaseHas('student_courses', ['id' => $oldest->id, 'is_current' => false]);
    }

    public function test_it_always_creates_the_first_course_as_current(): void
    {
        $student = Student::factory()->active()->create();
        $course = Course::factory()->create();

        $studentCourse = app(CreateStudentCourseAction::class)->execute(
            $student,
            new CreateStudentCourseData(courseId: $course->id, academicYear: 2026, isCurrent: false),
        );

        self::assertTrue($studentCourse->is_current);
        $this->assertDatabaseHas('student_courses', [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'is_current' => true,
        ]);
    }

    public function test_it_sets_a_historical_course_as_current_and_moves_the_previous_one_to_history(): void
    {
        [, $oldest, $previous, $current] = $this->studentWithHistory();

        app(SetCurrentStudentCourseAction::class)->execute($previous);

        $this->assertDatabaseHas('student_courses', ['id' => $previous->id, 'is_current' => true]);
        $this->assertDatabaseHas('student_courses', ['id' => $current->id, 'is_current' => false]);
        $this->assertDatabaseHas('student_courses', ['id' => $oldest->id, 'is_current' => false]);
    }

    /**
     * @return array{Student, StudentCourse, StudentCourse, StudentCourse}
     */
    private function studentWithHistory(): array
    {
        $student = Student::factory()->active()->create();

        return [
            $student,
            $this->enroll($student, 2023, isCurrent: false),
            $this->enroll($student, 2024, isCurrent: false),
            $this->enroll($student, 2025, isCurrent: true),
        ];
    }

    private function enroll(Student $student, int $academicYear, bool $isCurrent): StudentCourse
    {
        return StudentCourse::query()->create([
            'student_id' => $student->id,
            'course_id' => Course::factory()->create()->id,
            'academic_year' => $academicYear,
            'is_current' => $isCurrent,
        ]);
    }
}
