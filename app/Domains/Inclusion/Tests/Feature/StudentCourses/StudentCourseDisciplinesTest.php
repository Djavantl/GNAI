<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\StudentCourses;

use App\Domains\Inclusion\Application\Actions\Disciplines\DeleteDisciplineAction;
use App\Domains\Inclusion\Domain\Enums\StudentCourseDisciplineCategory;
use App\Domains\Inclusion\Domain\Exceptions\InvalidDiscipline;
use App\Domains\Inclusion\Domain\Models\Course;
use App\Domains\Inclusion\Domain\Models\Discipline;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentCourse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StudentCourseDisciplinesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_failed_and_at_risk_disciplines_independently(): void
    {
        $studentCourse = $this->createStudentCourse();
        $discipline = Discipline::factory()->create(['name' => 'Matemática']);

        $studentCourse->syncFailedDisciplines([$discipline->id]);
        $studentCourse->syncAtRiskDisciplines([$discipline->id]);
        $studentCourse->syncFailedDisciplines([]);

        $this->assertDatabaseMissing('student_course_disciplines', [
            'student_course_id' => $studentCourse->id,
            'category' => StudentCourseDisciplineCategory::FAILED->value,
        ]);
        $this->assertDatabaseHas('student_course_disciplines', [
            'student_course_id' => $studentCourse->id,
            'discipline_id' => $discipline->id,
            'category' => StudentCourseDisciplineCategory::AT_ACADEMIC_RISK->value,
        ]);
    }

    public function test_it_rejects_deleting_a_discipline_used_in_a_student_course_record(): void
    {
        $studentCourse = $this->createStudentCourse();
        $discipline = Discipline::factory()->create(['name' => 'Língua Portuguesa']);
        $studentCourse->syncAtRiskDisciplines([$discipline->id]);

        $this->expectException(InvalidDiscipline::class);
        $this->expectExceptionMessage(
            "Não é possível excluir a disciplina 'Língua Portuguesa' pois ela possui registros no histórico de cursos de alunos."
        );

        app(DeleteDisciplineAction::class)->execute($discipline);
    }

    private function createStudentCourse(): StudentCourse
    {
        return StudentCourse::query()->create([
            'student_id' => Student::factory()->active()->create()->id,
            'course_id' => Course::factory()->create()->id,
            'academic_year' => 2026,
            'is_current' => true,
        ]);
    }
}
