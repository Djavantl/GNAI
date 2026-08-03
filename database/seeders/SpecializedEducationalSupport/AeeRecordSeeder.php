<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AeeRecordSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = DB::table('attendance_sessions')
            ->where('session_date', '<=', Carbon::today()->format('Y-m-d'))
            ->where('attendance_type', AttendanceType::AEE->value)
            ->where('status', '!=', SessionStatus::CANCELLED_DATABASE_VALUE)
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('aee_records')
                    ->whereColumn('aee_records.attendance_session_id', 'attendance_sessions.id');
            })
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('pedagogical_records')
                    ->whereColumn('pedagogical_records.attendance_session_id', 'attendance_sessions.id');
            })
            ->get();

        if ($sessions->isEmpty()) {
            $this->command->warn('Nenhum agendamento AEE disponível para registrar.');

            return;
        }

        $createdRecords = 0;

        foreach ($sessions as $session) {
            $studentIds = DB::table('attendance_session_student')
                ->where('attendance_session_id', $session->id)
                ->pluck('student_id');

            if ($studentIds->isEmpty()) {
                $this->command->warn("Agendamento AEE #{$session->id} ignorado porque não possui alunos.");

                continue;
            }

            DB::transaction(function () use ($session, $studentIds): void {
                $aeeRecordId = DB::table('aee_records')->insertGetId([
                    'attendance_session_id' => $session->id,
                    'duration' => '1 hora',
                    'activities_performed' => 'Desenvolvimento de atividades lúdicas e suporte pedagógico adaptado.',
                    'strategies_used' => 'Mediação direta, reforço positivo e uso de materiais concretos.',
                    'resources_used' => 'Jogos educativos, prancha de comunicação alternativa e notebook.',
                    'general_observations' => 'O objetivo do agendamento foi atingido conforme o planejado para o período.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($studentIds as $studentId) {
                    $isPresent = random_int(1, 100) > 15;
                    $absenceReason = $isPresent ? null : $this->randomAbsenceReason();

                    DB::table('aee_student_evaluations')->insert([
                        'aee_record_id' => $aeeRecordId,
                        'student_id' => $studentId,
                        'is_present' => $isPresent,
                        'absence_reason' => $absenceReason,
                        'adaptations_made' => $isPresent ? 'Redução da complexidade das ordens verbais.' : null,
                        'student_participation' => $isPresent ? 'Engajado e colaborativo com o profissional.' : null,
                        'development_evaluation' => $isPresent ? 'Apresentou evolução na concentração.' : null,
                        'progress_indicators' => $isPresent ? 'Concluiu 80% das tarefas propostas.' : null,
                        'recommendations' => $isPresent ? 'Continuar com o suporte visual nas próximas aulas.' : null,
                        'next_session_adjustments' => $isPresent ? 'Introduzir novos elementos de alfabetização.' : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('attendance_sessions')->where('id', $session->id)->update([
                    'status' => SessionStatus::COMPLETED_DATABASE_VALUE,
                    'updated_at' => now(),
                ]);
            });

            $createdRecords++;
        }

        $this->command->info("{$createdRecords} registros AEE criados com suas avaliações.");
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
