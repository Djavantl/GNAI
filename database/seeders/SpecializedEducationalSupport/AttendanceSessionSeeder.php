<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Professional;
use Carbon\Carbon;

class AttendanceSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $professional = Professional::first(); // Profissional responsável

        if (!$professional) {
            $this->command->error("Nenhum profissional encontrado. Rode a PSPUSeeder primeiro.");
            return;
        }

        foreach ($students as $student) {
            // Gerar 15 sessões para cada aluno
            for ($i = 0; $i < 15; $i++) {
                
                // Lógica de datas: 10 sessões no passado e 5 no futuro
                // Subtrai de 1 a 10 semanas ou soma de 1 a 5 semanas
                if ($i < 10) {
                    $date = Carbon::now()->subWeeks($i + 1)->subDays(rand(0, 5));
                    $status = 'Realizada';
                } else {
                    $date = Carbon::now()->addWeeks($i - 9)->addDays(rand(0, 5));
                    $status = 'Agendada';
                }

                // 1. Criar a Sessão de Atendimento
                $sessionId = DB::table('attendance_sessions')->insertGetId([
                    'professional_id' => $professional->id,
                    'session_date'    => $date->format('Y-m-d'),
                    'start_time'      => '14:00:00',
                    'end_time'        => '15:00:00',
                    'type'            => $this->getRandomType(),
                    'location'        => 'Sala do AEE',
                    'session_objective' => 'Acompanhamento pedagógico e avaliação de progresso do PEI.',
                    'status'          => $status,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                // 2. Vincular o aluno à sessão (Relação Many-to-Many)
                DB::table('attendance_session_student')->insert([
                    'attendance_session_id' => $sessionId,
                    'student_id'            => $student->id,
                ]);
            }
        }
    }

    /**
     * Tipos aleatórios de atendimento
     */
    private function getRandomType(): string
    {
        $types = ['Individual', 'Avaliação', 'Apoio Pedagógico', 'Orientação'];
        return $types[array_rand($types)];
    }
}