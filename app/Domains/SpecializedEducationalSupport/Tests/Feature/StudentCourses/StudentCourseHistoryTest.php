<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Tests\Feature\StudentCourses;

use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses\CreateStudentCourseAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses\DeleteStudentCourseAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses\SetCurrentStudentCourseAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentCourses\CreateStudentCourseData;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentCourses\ListStudentCoursesData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentCourses\ListStudentCoursesQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class StudentCourseHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('people', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('students', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('person_id');
            $table->string('registration');
            $table->date('entry_date')->nullable();
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('student_courses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id');
            $table->foreignId('course_id');
            $table->unsignedInteger('academic_year');
            $table->boolean('is_current')->default(false);
            $table->longText('school_attendance_status')->nullable();
            $table->timestamps();
        });

        Schema::create('disciplines', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('course_disciplines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id');
            $table->foreignId('discipline_id');
            $table->timestamps();
        });
        Schema::create('student_course_disciplines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_course_id');
            $table->foreignId('discipline_id');
            $table->string('category');
            $table->timestamps();
        });

        DB::table('people')->insert([
            ['id' => 1, 'name' => 'Aluno com histórico', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Aluno sem curso', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('students')->insert([
            ['id' => 1, 'person_id' => 1, 'registration' => 'A001', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'person_id' => 2, 'registration' => 'A002', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('courses')->insert([
            ['id' => 1, 'name' => 'Curso antigo', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Curso intermediário', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Curso atual', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Primeiro curso', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('student_courses')->insert([
            ['id' => 1, 'student_id' => 1, 'course_id' => 1, 'academic_year' => 2023, 'is_current' => false, 'created_at' => '2023-01-01 00:00:00', 'updated_at' => now()],
            ['id' => 2, 'student_id' => 1, 'course_id' => 2, 'academic_year' => 2024, 'is_current' => false, 'created_at' => '2024-01-01 00:00:00', 'updated_at' => now()],
            ['id' => 3, 'student_id' => 1, 'course_id' => 3, 'academic_year' => 2025, 'is_current' => true, 'created_at' => '2025-01-01 00:00:00', 'updated_at' => now()],
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('student_course_disciplines');
        Schema::dropIfExists('course_disciplines');
        Schema::dropIfExists('disciplines');
        Schema::dropIfExists('student_courses');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('students');
        Schema::dropIfExists('people');

        parent::tearDown();
    }

    public function test_it_lists_the_current_and_most_recent_courses_first(): void
    {
        $student = Student::query()->findOrFail(1);

        $studentCourses = app(ListStudentCoursesQuery::class)->execute(
            $student,
            new ListStudentCoursesData(perPage: 10),
        );

        self::assertSame([3, 2, 1], $studentCourses->pluck('id')->all());
    }

    public function test_it_promotes_the_most_recent_previous_course_when_deleting_the_current_one(): void
    {
        $currentStudentCourse = StudentCourse::query()->findOrFail(3);

        app(DeleteStudentCourseAction::class)->execute($currentStudentCourse);

        $this->assertDatabaseMissing('student_courses', ['id' => 3]);
        $this->assertDatabaseHas('student_courses', ['id' => 2, 'is_current' => true]);
        $this->assertDatabaseHas('student_courses', ['id' => 1, 'is_current' => false]);
    }

    public function test_deleting_a_historical_course_does_not_change_the_current_course(): void
    {
        $historicalStudentCourse = StudentCourse::query()->findOrFail(2);

        app(DeleteStudentCourseAction::class)->execute($historicalStudentCourse);

        $this->assertDatabaseMissing('student_courses', ['id' => 2]);
        $this->assertDatabaseHas('student_courses', ['id' => 3, 'is_current' => true]);
        $this->assertDatabaseHas('student_courses', ['id' => 1, 'is_current' => false]);
    }

    public function test_the_first_course_is_always_created_as_current(): void
    {
        $student = Student::query()->findOrFail(2);

        $studentCourse = app(CreateStudentCourseAction::class)->execute(
            $student,
            new CreateStudentCourseData(courseId: 4, academicYear: 2026, isCurrent: false),
        );

        self::assertTrue($studentCourse->is_current);
        $this->assertDatabaseHas('student_courses', [
            'student_id' => 2,
            'course_id' => 4,
            'is_current' => true,
        ]);
    }

    public function test_it_sets_a_historical_course_as_current_and_moves_the_previous_one_to_history(): void
    {
        $historicalStudentCourse = StudentCourse::query()->findOrFail(2);

        app(SetCurrentStudentCourseAction::class)->execute($historicalStudentCourse);

        $this->assertDatabaseHas('student_courses', ['id' => 2, 'is_current' => true]);
        $this->assertDatabaseHas('student_courses', ['id' => 3, 'is_current' => false]);
        $this->assertDatabaseHas('student_courses', ['id' => 1, 'is_current' => false]);
    }
}
