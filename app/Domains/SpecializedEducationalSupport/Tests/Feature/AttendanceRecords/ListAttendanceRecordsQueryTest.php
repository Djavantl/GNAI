<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Tests\Feature\AttendanceRecords;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\ListAeeRecordsData;
use App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords\ListPedagogicalRecordsData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Students\ListStudentsData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\AeeRecords\ListAeeRecordsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\PedagogicalRecords\ListPedagogicalRecordsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Students\ListStudentsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Students\StudentAeeEvaluationsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Students\StudentPedagogicalRecordsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class ListAttendanceRecordsQueryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('people', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('professionals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('person_id');
            $table->timestamps();
        });
        Schema::create('students', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('person_id');
            $table->timestamps();
        });
        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('student_courses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id');
            $table->foreignId('course_id');
            $table->boolean('is_current');
            $table->timestamps();
        });
        Schema::create('deficiencies', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('students_deficiencies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id');
            $table->foreignId('deficiency_id');
            $table->string('severity')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('attendance_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('professional_id');
            $table->date('session_date');
            $table->time('start_time')->default('08:00:00');
            $table->time('end_time')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('attendance_session_student', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('attendance_session_id');
            $table->foreignId('student_id');
        });
        Schema::create('aee_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('attendance_session_id');
            $table->string('duration')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('aee_student_evaluations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('aee_record_id');
            $table->foreignId('student_id');
            $table->boolean('is_present');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pedagogical_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('attendance_session_id');
            $table->boolean('is_present');
            $table->string('duration')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('student_guardians', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id');
            $table->foreignId('person_id');
            $table->timestamps();
        });
        Schema::create('pedagogical_record_guardians', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pedagogical_record_id');
            $table->foreignId('guardian_id');
            $table->timestamps();
        });

        $this->seedRecords();
    }

    protected function tearDown(): void
    {
        foreach (['pedagogical_record_guardians', 'student_guardians', 'pedagogical_records', 'aee_student_evaluations', 'aee_records', 'attendance_session_student', 'attendance_sessions', 'students_deficiencies', 'deficiencies', 'student_courses', 'courses', 'students', 'professionals', 'people'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_aee_filters_can_be_combined_and_own_scope_is_respected(): void
    {
        $admin = new User(['is_admin' => true]);
        $admin->professional_id = 1;
        $filters = new ListAeeRecordsData(
            student: 1,
            professionalId: 2,
            isPresent: false,
        );

        $general = app(ListAeeRecordsQuery::class)->execute($filters, $admin);
        self::assertSame([2], $general->pluck('id')->all());

        $own = app(ListAeeRecordsQuery::class)->execute(new ListAeeRecordsData, $admin, onlyOwn: true);
        self::assertSame([1], $own->pluck('id')->all());

        $fromCourse = app(ListAeeRecordsQuery::class)->execute(ListAeeRecordsData::from(['course_id' => '1']), $admin);
        self::assertSame([2], $fromCourse->pluck('id')->all());

        $withoutCourse = app(ListAeeRecordsQuery::class)->execute(ListAeeRecordsData::from(['course_id' => '0']), $admin);
        self::assertSame([1], $withoutCourse->pluck('id')->all());
    }

    public function test_pedagogical_filters_can_be_combined_and_own_scope_is_respected(): void
    {
        $admin = new User(['is_admin' => true]);
        $admin->professional_id = 1;
        $filters = new ListPedagogicalRecordsData(
            student: 2,
            professionalId: 2,
            isPresent: true,
        );

        $general = app(ListPedagogicalRecordsQuery::class)->execute($filters, $admin);
        self::assertSame([2], $general->pluck('id')->all());

        $own = app(ListPedagogicalRecordsQuery::class)->execute(new ListPedagogicalRecordsData, $admin, onlyOwn: true);
        self::assertSame([1], $own->pluck('id')->all());

        $withGuardians = app(ListPedagogicalRecordsQuery::class)->execute(new ListPedagogicalRecordsData(withGuardians: true), $admin);
        self::assertSame([2], $withGuardians->pluck('id')->all());

        $withoutGuardians = app(ListPedagogicalRecordsQuery::class)->execute(new ListPedagogicalRecordsData(withGuardians: false), $admin);
        self::assertSame([1], $withoutGuardians->pluck('id')->all());

        $fromCourse = app(ListPedagogicalRecordsQuery::class)->execute(ListPedagogicalRecordsData::from(['course_id' => '1']), $admin);
        self::assertSame([1], $fromCourse->pluck('id')->all());

        $withoutCourse = app(ListPedagogicalRecordsQuery::class)->execute(ListPedagogicalRecordsData::from(['course_id' => '0']), $admin);
        self::assertSame([2], $withoutCourse->pluck('id')->all());
    }

    public function test_students_can_be_filtered_by_current_course_deficiency_or_missing_information(): void
    {
        $fromCourse = app(ListStudentsQuery::class)->execute(ListStudentsData::from(['course_id' => '1']));
        self::assertSame([1], $fromCourse->pluck('id')->all());

        $withoutCourse = app(ListStudentsQuery::class)->execute(ListStudentsData::from(['course_id' => '0']));
        self::assertSame([2], $withoutCourse->pluck('id')->all());

        $withDeficiency = app(ListStudentsQuery::class)->execute(ListStudentsData::from(['deficiency_id' => '1']));
        self::assertSame([2], $withDeficiency->pluck('id')->all());

        $withoutDeficiency = app(ListStudentsQuery::class)->execute(ListStudentsData::from(['deficiency_id' => '0']));
        self::assertSame([1], $withoutDeficiency->pluck('id')->all());
    }

    public function test_student_pedagogical_history_contains_all_records_in_chronological_order(): void
    {
        $now = now();
        DB::table('attendance_sessions')->insert([
            ['id' => 5, 'professional_id' => 1, 'session_date' => '2026-08-01', 'start_time' => '10:00:00', 'status' => 'Realizada', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'professional_id' => 1, 'session_date' => '2026-08-01', 'start_time' => '09:00:00', 'status' => 'Realizada', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'professional_id' => 1, 'session_date' => '2026-08-04', 'start_time' => '08:00:00', 'status' => 'Realizada', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('attendance_session_student')->insert([
            ['attendance_session_id' => 5, 'student_id' => 1],
            ['attendance_session_id' => 6, 'student_id' => 1],
            ['attendance_session_id' => 7, 'student_id' => 1],
        ]);
        DB::table('pedagogical_records')->insert([
            ['id' => 30, 'attendance_session_id' => 5, 'is_present' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 20, 'attendance_session_id' => 6, 'is_present' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'attendance_session_id' => 7, 'is_present' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $admin = new User(['is_admin' => true]);
        $student = Student::query()->findOrFail(1);

        $history = app(StudentPedagogicalRecordsQuery::class)->history($student, $admin);

        self::assertSame([20, 30, 1, 10], $history->pluck('id')->all());

        $student->registration = 'MAT000001';
        $student->load('person');
        $student->setRelation('currentCourse', null);
        $html = view(
            'pages.specialized-educational-support.pedagogical-records.student-history-pdf',
            ['student' => $student, 'pedagogicalRecords' => $history],
        )->render();

        self::assertStringContainsString('Histórico de Atendimentos Pedagógicos', $html);
        self::assertStringContainsString('MAT000001', $html);
        self::assertStringNotContainsString('Atendimento Pedagógico #', $html);
        self::assertStringNotContainsString('label">Agendamento', $html);
        self::assertStringNotContainsString('label">Atendimento', $html);
        preg_match_all('/Atendimento Pedagógico (\d{2}\/\d{2}\/\d{4})/', $html, $headings);
        self::assertSame(
            ['01/08/2026', '01/08/2026', '03/08/2026', '04/08/2026'],
            $headings[1],
        );
    }

    public function test_student_aee_history_contains_all_evaluations_in_chronological_order(): void
    {
        $now = now();
        DB::table('attendance_sessions')->insert([
            ['id' => 5, 'professional_id' => 1, 'session_date' => '2026-08-01', 'start_time' => '10:00:00', 'status' => 'Realizada', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'professional_id' => 1, 'session_date' => '2026-08-01', 'start_time' => '09:00:00', 'status' => 'Realizada', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'professional_id' => 1, 'session_date' => '2026-08-04', 'start_time' => '08:00:00', 'status' => 'Realizada', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('aee_records')->insert([
            ['id' => 30, 'attendance_session_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 20, 'attendance_session_id' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'attendance_session_id' => 7, 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('aee_student_evaluations')->insert([
            ['id' => 30, 'aee_record_id' => 30, 'student_id' => 1, 'is_present' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 20, 'aee_record_id' => 20, 'student_id' => 1, 'is_present' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'aee_record_id' => 10, 'student_id' => 1, 'is_present' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $admin = new User(['is_admin' => true]);
        $student = Student::query()->findOrFail(1);
        $history = app(StudentAeeEvaluationsQuery::class)->history($student, $admin);

        self::assertSame([20, 30, 2, 10], $history->pluck('id')->all());

        $student->registration = 'MAT000001';
        $student->load('person');
        $student->setRelation('currentCourse', null);
        $html = view(
            'pages.specialized-educational-support.aee-records.student-history-pdf',
            ['student' => $student, 'aeeEvaluations' => $history],
        )->render();

        self::assertStringContainsString('Histórico de Atendimentos Educacionais Especializados', $html);
        self::assertStringContainsString('MAT000001', $html);
        self::assertStringNotContainsString('Atendimento AEE #', $html);
        self::assertStringNotContainsString('label">Agendamento', $html);
        preg_match_all('/Atendimento AEE (\d{2}\/\d{2}\/\d{4})/', $html, $headings);
        self::assertSame(
            ['01/08/2026', '01/08/2026', '02/08/2026', '04/08/2026'],
            $headings[1],
        );
    }

    private function seedRecords(): void
    {
        $now = now();
        DB::table('people')->insert([
            ['id' => 1, 'name' => 'Profissional Um', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Profissional Dois', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Estudante Um', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Estudante Dois', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Responsável Dois', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('professionals')->insert([
            ['id' => 1, 'person_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'person_id' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('students')->insert([
            ['id' => 1, 'person_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'person_id' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('courses')->insert([
            ['id' => 1, 'name' => 'Curso Um', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Curso Dois', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('student_courses')->insert([
            ['student_id' => 1, 'course_id' => 1, 'is_current' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('deficiencies')->insert([
            ['id' => 1, 'name' => 'Deficiência Um', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('students_deficiencies')->insert([
            ['student_id' => 2, 'deficiency_id' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('attendance_sessions')->insert([
            ['id' => 1, 'professional_id' => 1, 'session_date' => '2026-08-01', 'status' => 'Realizada', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'professional_id' => 2, 'session_date' => '2026-08-02', 'status' => 'Realizada', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'professional_id' => 1, 'session_date' => '2026-08-03', 'status' => 'Realizada', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'professional_id' => 2, 'session_date' => '2026-08-04', 'status' => 'Realizada', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('attendance_session_student')->insert([
            ['attendance_session_id' => 3, 'student_id' => 1],
            ['attendance_session_id' => 4, 'student_id' => 2],
        ]);
        DB::table('aee_records')->insert([
            ['id' => 1, 'attendance_session_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'attendance_session_id' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('aee_student_evaluations')->insert([
            ['aee_record_id' => 1, 'student_id' => 2, 'is_present' => true, 'created_at' => $now, 'updated_at' => $now],
            ['aee_record_id' => 2, 'student_id' => 1, 'is_present' => false, 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('pedagogical_records')->insert([
            ['id' => 1, 'attendance_session_id' => 3, 'is_present' => false, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'attendance_session_id' => 4, 'is_present' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('student_guardians')->insert([
            'id' => 1,
            'student_id' => 2,
            'person_id' => 5,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('pedagogical_record_guardians')->insert([
            'pedagogical_record_id' => 2,
            'guardian_id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
