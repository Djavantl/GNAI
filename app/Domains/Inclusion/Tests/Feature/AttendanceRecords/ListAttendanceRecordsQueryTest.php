<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\AttendanceRecords;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Data\AeeRecords\ListAeeRecordsData;
use App\Domains\Inclusion\Application\Data\PedagogicalRecords\ListPedagogicalRecordsData;
use App\Domains\Inclusion\Application\Queries\AeeRecords\ListAeeRecordsQuery;
use App\Domains\Inclusion\Application\Queries\PedagogicalRecords\ListPedagogicalRecordsQuery;
use App\Domains\Inclusion\Application\Queries\Students\StudentAeeEvaluationsQuery;
use App\Domains\Inclusion\Application\Queries\Students\StudentPedagogicalRecordsQuery;
use App\Domains\Inclusion\Domain\Enums\AttendanceType;
use App\Domains\Inclusion\Domain\Models\AeeRecord;
use App\Domains\Inclusion\Domain\Models\AeeStudentEvaluation;
use App\Domains\Inclusion\Domain\Models\Course;
use App\Domains\Inclusion\Domain\Models\Guardian;
use App\Domains\Inclusion\Domain\Models\PedagogicalRecord;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\Models\Session;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentCourse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ListAttendanceRecordsQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_combines_aee_filters_and_respects_the_own_scope(): void
    {
        [$own, $other] = Professional::factory()->count(2)->create();
        $admin = User::factory()->create(['is_admin' => true, 'professional_id' => $own->id]);
        $enrolled = $this->enrolledStudent();
        $notEnrolled = Student::factory()->active()->create();
        $ownRecord = $this->aeeRecord($this->scheduleSession($own, '2026-08-01', AttendanceType::AEE), $notEnrolled, isPresent: true);
        $otherRecord = $this->aeeRecord($this->scheduleSession($other, '2026-08-02', AttendanceType::AEE), $enrolled, isPresent: false);
        $query = app(ListAeeRecordsQuery::class);

        $combined = $query->execute(new ListAeeRecordsData(student: $enrolled->id, professionalId: $other->id, isPresent: false), $admin);
        $onlyOwn = $query->execute(new ListAeeRecordsData, $admin, onlyOwn: true);
        $withCourse = $query->execute(ListAeeRecordsData::from(['course_id' => (string) $enrolled->currentCourse->course_id]), $admin);
        $withoutCourse = $query->execute(ListAeeRecordsData::from(['course_id' => '0']), $admin);

        self::assertSame([$otherRecord->id], $combined->pluck('id')->all());
        self::assertSame([$ownRecord->id], $onlyOwn->pluck('id')->all());
        self::assertSame([$otherRecord->id], $withCourse->pluck('id')->all());
        self::assertSame([$ownRecord->id], $withoutCourse->pluck('id')->all());
    }

    public function test_it_combines_pedagogical_filters_and_respects_the_own_scope(): void
    {
        [$own, $other] = Professional::factory()->count(2)->create();
        $admin = User::factory()->create(['is_admin' => true, 'professional_id' => $own->id]);
        $enrolled = $this->enrolledStudent();
        $notEnrolled = Student::factory()->active()->create();
        $ownRecord = $this->pedagogicalRecord($this->scheduleSession($own, '2026-08-03', AttendanceType::PEDAGOGICAL, $enrolled), isPresent: false);
        $otherRecord = $this->pedagogicalRecord($this->scheduleSession($other, '2026-08-04', AttendanceType::PEDAGOGICAL, $notEnrolled), isPresent: true);
        $otherRecord->guardians()->attach(Guardian::factory()->create(['student_id' => $notEnrolled->id]));
        $query = app(ListPedagogicalRecordsQuery::class);

        $combined = $query->execute(new ListPedagogicalRecordsData(student: $notEnrolled->id, professionalId: $other->id, isPresent: true), $admin);
        $onlyOwn = $query->execute(new ListPedagogicalRecordsData, $admin, onlyOwn: true);
        $withGuardians = $query->execute(new ListPedagogicalRecordsData(withGuardians: true), $admin);
        $withoutGuardians = $query->execute(new ListPedagogicalRecordsData(withGuardians: false), $admin);
        $withCourse = $query->execute(ListPedagogicalRecordsData::from(['course_id' => (string) $enrolled->currentCourse->course_id]), $admin);
        $withoutCourse = $query->execute(ListPedagogicalRecordsData::from(['course_id' => '0']), $admin);

        self::assertSame([$otherRecord->id], $combined->pluck('id')->all());
        self::assertSame([$ownRecord->id], $onlyOwn->pluck('id')->all());
        self::assertSame([$otherRecord->id], $withGuardians->pluck('id')->all());
        self::assertSame([$ownRecord->id], $withoutGuardians->pluck('id')->all());
        self::assertSame([$ownRecord->id], $withCourse->pluck('id')->all());
        self::assertSame([$otherRecord->id], $withoutCourse->pluck('id')->all());
    }

    public function test_it_orders_the_student_pedagogical_history_by_session_date_and_time(): void
    {
        $professional = Professional::factory()->create();
        $student = Student::factory()->active()->create();
        $latest = $this->pedagogicalRecord($this->scheduleSession($professional, '2026-08-04', AttendanceType::PEDAGOGICAL, $student, '08:00:00'), isPresent: true);
        $afternoon = $this->pedagogicalRecord($this->scheduleSession($professional, '2026-08-01', AttendanceType::PEDAGOGICAL, $student, '14:00:00'), isPresent: true);
        $morning = $this->pedagogicalRecord($this->scheduleSession($professional, '2026-08-01', AttendanceType::PEDAGOGICAL, $student, '09:00:00'), isPresent: true);

        $history = app(StudentPedagogicalRecordsQuery::class)->history($student, User::factory()->create(['is_admin' => true]));

        self::assertSame([$morning->id, $afternoon->id, $latest->id], $history->pluck('id')->all());
    }

    public function test_it_orders_the_student_aee_history_by_session_date_and_time(): void
    {
        $professional = Professional::factory()->create();
        $student = Student::factory()->active()->create();
        $latest = $this->aeeRecord($this->scheduleSession($professional, '2026-08-04', AttendanceType::AEE, time: '08:00:00'), $student, isPresent: true);
        $afternoon = $this->aeeRecord($this->scheduleSession($professional, '2026-08-01', AttendanceType::AEE, time: '14:00:00'), $student, isPresent: true);
        $morning = $this->aeeRecord($this->scheduleSession($professional, '2026-08-01', AttendanceType::AEE, time: '09:00:00'), $student, isPresent: true);

        $history = app(StudentAeeEvaluationsQuery::class)->history($student, User::factory()->create(['is_admin' => true]));

        self::assertSame(
            [$morning->id, $afternoon->id, $latest->id],
            $history->pluck('aee_record_id')->all(),
        );
    }

    private function enrolledStudent(): Student
    {
        $student = Student::factory()->active()->create();
        StudentCourse::query()->create([
            'student_id' => $student->id,
            'course_id' => Course::factory()->create()->id,
            'academic_year' => 2026,
            'is_current' => true,
        ]);

        return $student;
    }

    private function scheduleSession(
        Professional $professional,
        string $date,
        AttendanceType $attendanceType,
        ?Student $student = null,
        string $time = '08:00:00',
    ): Session {
        $session = Session::query()->create([
            'professional_id' => $professional->id,
            'session_date' => $date,
            'start_time' => $time,
            'type' => 'individual',
            'attendance_type' => $attendanceType,
            'location' => 'Sala de recursos',
            'session_objective' => 'Acompanhamento',
            'status' => 'completed',
        ]);

        if ($student !== null) {
            $session->students()->attach($student);
        }

        return $session;
    }

    private function aeeRecord(Session $session, Student $student, bool $isPresent): AeeRecord
    {
        $record = AeeRecord::query()->create([
            'attendance_session_id' => $session->id,
            'duration' => '50 minutos',
            'activities_performed' => 'Atividades adaptadas.',
        ]);
        AeeStudentEvaluation::query()->create([
            'aee_record_id' => $record->id,
            'student_id' => $student->id,
            'is_present' => $isPresent,
        ]);

        return $record;
    }

    private function pedagogicalRecord(Session $session, bool $isPresent): PedagogicalRecord
    {
        return PedagogicalRecord::query()->create([
            'attendance_session_id' => $session->id,
            'duration' => '50 minutos',
            'is_present' => $isPresent,
        ]);
    }
}
