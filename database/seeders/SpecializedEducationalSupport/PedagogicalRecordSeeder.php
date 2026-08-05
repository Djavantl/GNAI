<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
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

                DB::table('pedagogical_records')->insert([
                    'attendance_session_id' => $session->id,
                    'follow_up_reason' => 'Necessidade de acompanhamento do desempenho acadêmico e da participação escolar do estudante.',
                    'duration' => '1 hora',
                    'is_present' => $isPresent,
                    'absence_reason' => $isPresent ? null : $this->randomAbsenceReason(),
                    'systematic_pedagogical_follow_up_record' => $isPresent ? 'O aluno participou das atividades propostas e apresentou evolução durante o acompanhamento.' : null,
                    'strategies_and_resources_adopted' => $isPresent ? 'Material didático adaptado, recursos visuais, atividades impressas e mediação individualizada.' : null,
                    'referrals_made' => $isPresent ? 'Orientação aos docentes e contato com a equipe pedagógica para acompanhamento.' : null,
                    'complementary_observations' => $isPresent ? 'O atendimento ocorreu conforme o planejamento pedagógico.' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

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
}
