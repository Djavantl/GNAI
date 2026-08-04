<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Tests\Feature\AttendanceRecords;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\ListAeeRecordsData;
use App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords\ListPedagogicalRecordsData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\AeeRecords\ListAeeRecordsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\PedagogicalRecords\ListPedagogicalRecordsQuery;
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
            $table->string('follow_up_status')->nullable();
            $table->string('duration')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->seedRecords();
    }

    protected function tearDown(): void
    {
        foreach (['pedagogical_records', 'aee_student_evaluations', 'aee_records', 'attendance_session_student', 'attendance_sessions', 'students', 'professionals', 'people'] as $table) {
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
    }

    private function seedRecords(): void
    {
        $now = now();
        DB::table('people')->insert([
            ['id' => 1, 'name' => 'Profissional Um', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Profissional Dois', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Estudante Um', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Estudante Dois', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('professionals')->insert([
            ['id' => 1, 'person_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'person_id' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('students')->insert([
            ['id' => 1, 'person_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'person_id' => 4, 'created_at' => $now, 'updated_at' => $now],
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
            ['id' => 1, 'attendance_session_id' => 3, 'is_present' => false, 'follow_up_status' => 'ongoing', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'attendance_session_id' => 4, 'is_present' => true, 'follow_up_status' => 'completed', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
