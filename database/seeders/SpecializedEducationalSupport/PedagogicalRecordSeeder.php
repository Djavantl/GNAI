<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\PedagogicalDisciplineCategory;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\PedagogicalFollowUpStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PedagogicalRecordSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = DB::table('attendance_sessions')
            ->where('session_date', '<=', Carbon::today()->format('Y-m-d'))
            ->where('attendance_type', AttendanceType::PEDAGOGICAL->value)
            ->where('status', '!=', SessionStatus::CANCELLED_DATABASE_VALUE)
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('pedagogical_records')
                    ->whereColumn('pedagogical_records.attendance_session_id', 'attendance_sessions.id');
            })
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('aee_records')
                    ->whereColumn('aee_records.attendance_session_id', 'attendance_sessions.id');
            })
            ->get();

        if ($sessions->isEmpty()) {
            $this->command->warn('Nenhum agendamento pedagógico disponível para registrar.');

            return;
        }

        $createdRecords = 0;

        foreach ($sessions as $session) {
            $studentCount = DB::table('attendance_session_student')->where('attendance_session_id', $session->id)->count();

            if ($studentCount !== 1) {
                $this->command->warn("Agendamento pedagógico #{$session->id} ignorado porque deve possuir exatamente um aluno.");

                continue;
            }

            DB::transaction(function () use ($session): void {
                $isPresent = random_int(1, 100) > 15;

                $recordId = DB::table('pedagogical_records')->insertGetId([
                    'attendance_session_id' => $session->id,
                    'follow_up_reason' => 'Necessidade de acompanhamento do desempenho acadêmico e da participação escolar do estudante.',
                    'follow_up_status' => $this->randomFollowUpStatus()->value,
                    'duration' => '1 hora',
                    'is_present' => $isPresent,
                    'absence_reason' => $isPresent ? null : $this->randomAbsenceReason(),
                    'systematic_pedagogical_follow_up_record' => $isPresent ? 'O aluno participou das atividades propostas e apresentou evolução durante o acompanhamento.' : null,
                    'strategies_and_resources_adopted' => $isPresent ? 'Material didático adaptado, recursos visuais, atividades impressas e mediação individualizada.' : null,
                    'school_attendance_status' => $isPresent ? 'Frequência regular, sem ausências recorrentes no período.' : null,
                    'referrals_made' => $isPresent ? 'Orientação aos docentes e contato com a equipe pedagógica para acompanhamento.' : null,
                    'complementary_observations' => $isPresent ? 'O atendimento ocorreu conforme o planejamento pedagógico.' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($isPresent) {
                    $this->seedDisciplines($recordId, (int) $session->id);
                }

                DB::table('attendance_sessions')->where('id', $session->id)->update([
                    'status' => SessionStatus::COMPLETED_DATABASE_VALUE,
                    'updated_at' => now(),
                ]);
            });

            $createdRecords++;
        }

        $this->command->info("{$createdRecords} registros pedagógicos criados.");
    }

    private function randomAbsenceReason(): string
    {
        $reasons = [
            'Problemas de saúde na família.',
            'Falta de transporte escolar.',
            'Consulta médica agendada.',
            'O aluno não se sentiu bem no dia.',
        ];

        return $reasons[array_rand($reasons)];
    }

    private function randomFollowUpStatus(): PedagogicalFollowUpStatus
    {
        $statuses = PedagogicalFollowUpStatus::cases();

        return $statuses[array_rand($statuses)];
    }

    private function seedDisciplines(int $recordId, int $sessionId): void
    {
        $studentId = DB::table('attendance_session_student')
            ->where('attendance_session_id', $sessionId)
            ->value('student_id');
        $courseId = DB::table('student_courses')
            ->where('student_id', $studentId)
            ->where('is_current', true)
            ->value('course_id');
        $disciplineIds = DB::table('course_disciplines')
            ->where('course_id', $courseId)
            ->pluck('discipline_id')
            ->shuffle()
            ->values();
        $now = now();
        $rows = [];

        if ($disciplineIds->isNotEmpty()) {
            $rows[] = [
                'pedagogical_record_id' => $recordId,
                'discipline_id' => $disciplineIds->first(),
                'category' => PedagogicalDisciplineCategory::FAILED->value,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($disciplineIds->slice(1, 2) as $disciplineId) {
            $rows[] = [
                'pedagogical_record_id' => $recordId,
                'discipline_id' => $disciplineId,
                'category' => PedagogicalDisciplineCategory::AT_ACADEMIC_RISK->value,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== []) {
            DB::table('pedagogical_record_disciplines')->insert($rows);
        }
    }
}
