<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Professional;
use Carbon\Carbon;

class AttendanceSessionSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all()->values();
        $professionals = Professional::all()->values();

        if ($professionals->isEmpty()) {
            $this->command->error('Nenhum profissional encontrado.');
            return;
        }

        if ($students->isEmpty()) {
            $this->command->error('Nenhum aluno encontrado.');
            return;
        }

        // Máximo de 15 sessões
        $maxSessions = min(15, $students->count());
        $usedDates = [];

        for ($i = 0; $i < $maxSessions; $i++) {

            // DEFINIÇÃO DO PROFESSIONAL (aqui estava faltando)
            $professional = $professionals[$i % $professionals->count()];

            // Alterna tipo
            $type = ($i % 3 === 0) ? 'group' : 'individual';

            if ($type === 'group' && $students->count() < 2) {
                $type = 'individual';
            }

            $date = $this->generateUniqueDate($usedDates, $i);

            $sessionId = DB::table('attendance_sessions')->insertGetId([
                'professional_id'   => $professional->id,
                'session_date'      => $date->format('Y-m-d'),
                'start_time'        => $i % 2 === 0 ? '08:00:00' : '14:00:00',
                'end_time'          => $i % 2 === 0 ? '09:00:00' : '15:00:00',
                'type'              => $type,
                'location'          => 'Sala do AEE',
                'session_objective' => 'Acompanhamento pedagógico e evolução do atendimento especializado.',
                'status'            => 'Agendada',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $selectedStudents = $this->pickStudentsForSession($students, $type);

            foreach ($selectedStudents as $student) {
                DB::table('attendance_session_student')->insert([
                    'attendance_session_id' => $sessionId,
                    'student_id'            => $student->id,
                ]);
            }
        }
    }

    private function pickStudentsForSession($students, string $type)
    {
        if ($type === 'individual') {
            return collect([$students->random()]);
        }

        $quantity = min(3, $students->count());

        return $students
            ->shuffle()
            ->take($quantity)
            ->values();
    }

    private function generateUniqueDate(array &$usedDates, int $index): Carbon
    {
        do {
            $date = Carbon::now()
                ->subDays(rand(1, 120))
                ->addDays($index);

            $dateKey = $date->format('Y-m-d');

        } while (in_array($dateKey, $usedDates, true));

        $usedDates[] = $dateKey;

        return $date;
    }
}