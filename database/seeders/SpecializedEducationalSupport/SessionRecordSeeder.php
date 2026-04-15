<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SpecializedEducationalSupport\Session; // Ajuste o namespace se necessário
use Carbon\Carbon;

class SessionRecordSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pegar todas as sessões que já aconteceram (até hoje)
        // Usamos o status 'Agendada' para não duplicar se rodar a seeder 2x
        $sessions = DB::table('attendance_sessions')
            ->where('session_date', '<=', Carbon::today()->format('Y-m-d'))
            ->where('status', '!=', 'Cancelada')
            ->get();

        if ($sessions->isEmpty()) {
            $this->command->warn('Nenhuma sessão passada encontrada para registrar.');
            return;
        }

        foreach ($sessions as $session) {
            // 2. Criar o Registro Geral da Sessão (SessionRecord)
            $recordId = DB::table('session_records')->insertGetId([
                'attendance_session_id' => $session->id,
                'duration'              => '1 hora', // Padrão baseado nos seus slots
                'activities_performed'  => 'Desenvolvimento de atividades lúdicas e suporte pedagógico adaptado.',
                'strategies_used'       => 'Mediação direta, reforço positivo e uso de materiais concretos.',
                'resources_used'        => 'Jogos educativos, prancha de comunicação alternativa e notebook.',
                'general_observations'  => 'O objetivo da sessão foi atingido conforme o planejado para o período.',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            // 3. Buscar os alunos vinculados a esta sessão
            $studentIds = DB::table('attendance_session_student')
                ->where('attendance_session_id', $session->id)
                ->pluck('student_id');

            foreach ($studentIds as $studentId) {
                // Lógica de Falta: 15% de chance de estar ausente
                $isPresent = (rand(1, 100) > 15);
                $absenceReason = null;

                if (!$isPresent) {
                    $reasons = [
                        'Problemas de saúde na família.',
                        'Falta de transporte escolar.',
                        'Consulta médica agendada.',
                        'O aluno não se sentiu bem no dia.',
                    ];
                    $absenceReason = $reasons[array_rand($reasons)];
                }

                // 4. Criar a Avaliação do Aluno (StudentSessionEvaluation)
                DB::table('student_session_evaluations')->insert([
                    'session_record_id'        => $recordId,
                    'student_id'               => $studentId,
                    'is_present'               => $isPresent,
                    'absence_reason'           => $absenceReason,
                    
                    // Se estiver presente, preenche os dados, senão nulo
                    'adaptations_made'         => $isPresent ? 'Redução da complexidade das ordens verbais.' : null,
                    'student_participation'    => $isPresent ? 'Engajado e colaborativo com o profissional.' : null,
                    'development_evaluation'   => $isPresent ? 'Apresentou evolução na concentração.' : null,
                    'progress_indicators'      => $isPresent ? 'Concluiu 80% das tarefas propostas.' : null,
                    'recommendations'          => $isPresent ? 'Continuar com o suporte visual nas próximas aulas.' : null,
                    'next_session_adjustments' => $isPresent ? 'Introduzir novos elementos de alfabetização.' : null,
                    
                    'created_at'               => now(),
                    'updated_at'               => now(),
                ]);
            }

            // 5. Atualizar o status da sessão original para 'Realizada'
            DB::table('attendance_sessions')
                ->where('id', $session->id)
                ->update(['status' => 'Realizada']);
        }

        $this->command->info("Registros e avaliações criados para {$sessions->count()} sessões.");
    }
}