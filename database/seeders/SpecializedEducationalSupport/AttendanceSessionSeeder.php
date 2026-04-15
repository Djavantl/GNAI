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
     * 8 slots de horário disponíveis por dia (seg–sex).
     * Cada slot é garantidamente não-sobreposto, então basta controlar
     * qual slot cada profissional/aluno já usa em determinado dia.
     */
    private array $timeSlots = [
        ['08:00:00', '09:00:00'],
        ['09:00:00', '10:00:00'],
        ['10:00:00', '11:00:00'],
        ['11:00:00', '12:00:00'],
        ['14:00:00', '15:00:00'],
        ['15:00:00', '16:00:00'],
        ['16:00:00', '17:00:00'],
        ['17:00:00', '18:00:00'],
    ];

    public function run(): void
    {
        $students      = Student::all()->values();
        $professionals = Professional::all()->values();

        if ($professionals->isEmpty()) {
            $this->command->error('Nenhum profissional encontrado.');
            return;
        }

        if ($students->isEmpty()) {
            $this->command->error('Nenhum aluno encontrado.');
            return;
        }

        // Semana passada, atual e próxima — apenas seg a sex
        $weeks = $this->getTargetWeeks();

        // Rastreia quais slots já foram usados para evitar conflitos:
        // [professional_id][date][slot_index] = true
        // [student_id][date][slot_index]      = true
        $professionalSlots = [];
        $studentSlots      = [];

        $sessionIndex = 0;

        foreach ($weeks as $weekDays) {
            $sessionsThisWeek = 0;

            // Embaralha os dias para distribuição mais natural
            $shuffledDays = collect($weekDays)->shuffle()->values();
            $totalDays    = $shuffledDays->count(); // sempre 5

            foreach ($shuffledDays as $dayPosition => $date) {
                if ($sessionsThisWeek >= 15) {
                    break;
                }

                // Quantas sessões ainda faltam e quantos dias restam (incluindo o atual)
                $remaining    = 15 - $sessionsThisWeek;
                $daysLeft     = $totalDays - $dayPosition;
                // Distribuição: ceil para cobrir as 8 sem sobrecarregar um único dia (máx 2)
                $sessionsToday = (int) min(ceil($remaining / $daysLeft), 4);

                $createdToday = 0;

                $shuffledSlots = collect($this->timeSlots)->shuffle()->values();
                foreach ($shuffledSlots as $slotIndex => [$startTime, $endTime]) {
                    if ($createdToday >= $sessionsToday) {
                        break;
                    }

                    // Escolhe o profissional disponível nesse slot
                    $professional = $this->pickAvailableProfessional(
                        $professionals,
                        $date,
                        $slotIndex,
                        $professionalSlots
                    );

                    if (!$professional) {
                        continue; // todos ocupados nesse slot, tenta o próximo
                    }

                    // Define tipo: a cada 3 sessões uma é em grupo
                    $type = ($sessionIndex % 3 === 0) ? 'group' : 'individual';

                    if ($type === 'group' && $students->count() < 2) {
                        $type = 'individual';
                    }

                    // Seleciona alunos disponíveis nesse slot
                    $selectedStudents = $this->pickAvailableStudents(
                        $students,
                        $type,
                        $date,
                        $slotIndex,
                        $studentSlots
                    );

                    if ($selectedStudents->isEmpty()) {
                        continue; // alunos insuficientes disponíveis, tenta o próximo slot
                    }

                    // --- Insere a sessão ---
                    $sessionId = DB::table('attendance_sessions')->insertGetId([
                        'professional_id'   => $professional->id,
                        'session_date'      => $date,
                        'start_time'        => $startTime,
                        'end_time'          => $endTime,
                        'type'              => $type,
                        'location'          => 'Sala do AEE',
                        'session_objective' => 'Acompanhamento pedagógico e evolução do atendimento especializado.',
                        'status'            => $this->resolveStatus($date),
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);

                    foreach ($selectedStudents as $student) {
                        DB::table('attendance_session_student')->insert([
                            'attendance_session_id' => $sessionId,
                            'student_id'            => $student->id,
                        ]);

                        // Marca o slot como ocupado para o aluno
                        $studentSlots[$student->id][$date][$slotIndex] = true;
                    }

                    // Marca o slot como ocupado para o profissional
                    $professionalSlots[$professional->id][$date][$slotIndex] = true;

                    $createdToday++;
                    $sessionsThisWeek++;
                    $sessionIndex++;
                }
            }

            $this->command->info("Semana iniciada em {$weekDays[0]}: {$sessionsThisWeek} sessões criadas.");
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Retorna os dias úteis (seg–sex) das 3 semanas alvo.
     * Cada semana é um array de 5 strings 'Y-m-d'.
     */
    private function getTargetWeeks(): array
    {
        $now   = Carbon::now();
        $weeks = [];

        foreach ([-1, 0, 1] as $offset) {
            $monday   = $now->copy()->addWeeks($offset)->startOfWeek(Carbon::MONDAY);
            $weekDays = [];

            for ($d = 0; $d < 5; $d++) {
                $weekDays[] = $monday->copy()->addDays($d)->format('Y-m-d');
            }

            $weeks[] = $weekDays;
        }

        return $weeks;
    }

    /**
     * Retorna o primeiro profissional disponível no slot do dia.
     */
    private function pickAvailableProfessional(
        $professionals,
        string $date,
        int $slotIndex,
        array $professionalSlots
    ) {
        foreach ($professionals->shuffle() as $professional) {
            $occupied = $professionalSlots[$professional->id][$date][$slotIndex] ?? false;

            if (!$occupied) {
                return $professional;
            }
        }

        return null;
    }

    /**
     * Seleciona alunos disponíveis no slot, respeitando o tipo de sessão.
     */
    private function pickAvailableStudents(
        $students,
        string $type,
        string $date,
        int $slotIndex,
        array $studentSlots
    ) {
        $available = $students->filter(function ($student) use ($date, $slotIndex, $studentSlots) {
            return !($studentSlots[$student->id][$date][$slotIndex] ?? false);
        })->values();

        if ($available->isEmpty()) {
            return collect();
        }

        if ($type === 'individual') {
            return collect([$available->first()]);
        }

        // Grupo: precisa de pelo menos 2 alunos disponíveis
        if ($available->count() < 2) {
            return collect();
        }

        return $available->shuffle()->take(min(3, $available->count()))->values();
    }

    /**
     * Define o status da sessão com base na data:
     * - Passado → 'Realizada'
     * - Hoje/Futuro → 'Agendada'
     */
    private function resolveStatus(string $date): string
    {
        return Carbon::parse($date)->startOfDay()->lt(Carbon::today())
            ? 'Realizada'
            : 'Agendada';
    }
}