<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Tests\Feature\StudentCourses;

use App\Domains\SpecializedEducationalSupport\Application\Queries\Disciplines\DisciplineHasStudentCourseRecordsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentCourseDisciplineCategory;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidDiscipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class StudentCourseDisciplinesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('student_courses', function (Blueprint $table): void {
            $table->id();
            $table->longText('school_attendance_status')->nullable();
            $table->timestamps();
        });
        Schema::create('disciplines', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('student_course_disciplines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('discipline_id')->constrained()->restrictOnDelete();
            $table->string('category');
            $table->timestamps();
            $table->unique(['student_course_id', 'discipline_id', 'category']);
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('student_course_disciplines');
        Schema::dropIfExists('disciplines');
        Schema::dropIfExists('student_courses');

        parent::tearDown();
    }

    public function test_it_syncs_failed_and_at_risk_disciplines_independently(): void
    {
        $studentCourseId = DB::table('student_courses')->insertGetId([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $disciplineId = DB::table('disciplines')->insertGetId([
            'name' => 'Matemática',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $studentCourse = StudentCourse::query()->findOrFail($studentCourseId);

        $studentCourse->syncFailedDisciplines([$disciplineId]);
        $studentCourse->syncAtRiskDisciplines([$disciplineId]);
        $studentCourse->unsetRelation('failedDisciplines');
        $studentCourse->unsetRelation('atRiskDisciplines');

        self::assertSame('Matemática', $studentCourse->failed_discipline_names);
        self::assertSame('Matemática', $studentCourse->at_risk_discipline_names);
        $this->assertDatabaseHas('student_course_disciplines', [
            'student_course_id' => $studentCourseId,
            'discipline_id' => $disciplineId,
            'category' => StudentCourseDisciplineCategory::FAILED->value,
        ]);
        $this->assertDatabaseHas('student_course_disciplines', [
            'student_course_id' => $studentCourseId,
            'discipline_id' => $disciplineId,
            'category' => StudentCourseDisciplineCategory::AT_ACADEMIC_RISK->value,
        ]);

        $studentCourse->syncFailedDisciplines([]);

        $this->assertDatabaseMissing('student_course_disciplines', [
            'student_course_id' => $studentCourseId,
            'category' => StudentCourseDisciplineCategory::FAILED->value,
        ]);
        $this->assertDatabaseHas('student_course_disciplines', [
            'student_course_id' => $studentCourseId,
            'category' => StudentCourseDisciplineCategory::AT_ACADEMIC_RISK->value,
        ]);
    }

    public function test_it_prevents_deleting_a_discipline_used_in_a_student_course_record(): void
    {
        $studentCourseId = DB::table('student_courses')->insertGetId(['created_at' => now(), 'updated_at' => now()]);
        $disciplineId = DB::table('disciplines')->insertGetId([
            'name' => 'Língua Portuguesa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('student_course_disciplines')->insert([
            'student_course_id' => $studentCourseId,
            'discipline_id' => $disciplineId,
            'category' => StudentCourseDisciplineCategory::AT_ACADEMIC_RISK->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $discipline = Discipline::query()->findOrFail($disciplineId);
        $hasRecords = app(DisciplineHasStudentCourseRecordsQuery::class)->execute($discipline);

        self::assertTrue($hasRecords);

        try {
            $discipline->ensureCanBeDeleted(
                hasTeachers: false,
                hasCourses: false,
                hasStudentCourseRecords: $hasRecords,
            );
            self::fail('A exclusão deveria ter sido impedida.');
        } catch (InvalidDiscipline $exception) {
            self::assertSame(
                "Não é possível excluir a disciplina 'Língua Portuguesa' pois ela possui registros no histórico de cursos de alunos.",
                $exception->getMessage(),
            );
        }

        try {
            DB::table('disciplines')->where('id', $disciplineId)->delete();
            self::fail('A restrição do banco deveria ter impedido a exclusão.');
        } catch (QueryException) {
            $this->addToAssertionCount(1);
        }
    }
}
