<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SessionNotification;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class SessionService
{
    
    public function index(array $filters = [])
    {
        return Session::query()
            ->with([
                'students.person',
                'professional.person',
                'sessionRecord'
            ])

            ->student($filters['student'] ?? null)
            ->professional($filters['professional'] ?? null)
            ->type($filters['type'] ?? null)
            ->status($filters['status'] ?? null)
            
            ->orderByDesc('session_date')
            ->paginate(10)
            ->withQueryString();
    }

    //email

    private function sendSessionEmails(Session $session, string $subject, string $text)
    {
        $session->load(['students.person', 'professional.person']);

        $emails = [];

        foreach ($session->students as $student) {
            if ($student->person->email) {
                $emails[] = $student->person->email;
            }
        }

        if ($session->professional->person->email) {
            $emails[] = $session->professional->person->email;
        }

        foreach ($emails as $email) {
            Mail::to($email)->send(
                new SessionNotification($session, $subject, $text)
            );
        }
    }

    private function normalizeTime(array &$data): void
    {
        // Garante que start_time não tenha segundos lixo
        $data['start_time'] = Carbon::parse($data['start_time'])->format('H:i');

        if (empty($data['end_time'])) {
            $data['end_time'] = Carbon::parse($data['start_time'])->addHour()->format('H:i');
        } else {
            $data['end_time'] = Carbon::parse($data['end_time'])->format('H:i');
        }
    }

    private function detectConflict(array $data, ?int $ignoreId = null): array
    {
        $date = $data['session_date'];
        
        // Forçamos o formato H:i:00 para garantir que não existam segundos perdidos
        $start = Carbon::parse($data['start_time'])->format('H:i:00');
        $end = Carbon::parse($data['end_time'])->format('H:i:00');

        $baseQuery = Session::whereDate('session_date', $date)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where(function ($query) use ($start, $end) {
                // A lógica correta de intersecção:
                // (InícioExistente < FimNovo) E (FimExistente > InícioNovo)
                $query->whereTime('start_time', '<', $end)
                    ->whereTime('end_time', '>', $start);
            });

        // 1. Conflito do profissional
        $professionalConflict = (clone $baseQuery)
            ->where('professional_id', $data['professional_id'])
            ->exists();

        // 2. Conflito de alunos
        $studentIds = $data['student_ids'] ?? [];
        
        $conflictingStudents = (clone $baseQuery)
            ->whereHas('students', function ($q) use ($studentIds) {
                $q->whereIn('students.id', $studentIds);
            })
            ->with('students.person')
            ->get()
            ->pluck('students')
            ->flatten()
            ->filter(fn($s) => in_array($s->id, $studentIds))
            ->mapWithKeys(fn($s) => [$s->id => $s->person->name])
            ->toArray();

        return [
            'students' => $conflictingStudents,
            'professional' => $professionalConflict,
            'hasConflict' => $professionalConflict || !empty($conflictingStudents)
        ];
    }



    // criar

    public function create(array $data): Session
    {
        return DB::transaction(function () use ($data) {
            $this->normalizeTime($data);
            $conflict = $this->detectConflict($data);

            if ($conflict['hasConflict']) {
                $errors = [];

                if (!empty($conflict['students'])) {
                    // $conflict['students'] agora contém os nomes
                    $names = implode(', ', $conflict['students']);
                    $errors['student_ids'] = "Conflito de agenda para: {$names}.";
                }

                if ($conflict['professional']) {
                    $errors['professional_id'] = "O profissional já possui uma sessão neste horário.";
                }

                throw ValidationException::withMessages($errors);
            }

            $session = Session::create([
                'professional_id'   => $data['professional_id'],
                'session_date'      => $data['session_date'],
                'start_time'        => $data['start_time'],
                'end_time'          => $data['end_time'],
                'type'              => $data['type'],
                'location'          => $data['location'] ?? null,
                'session_objective' => $data['session_objective'],
                'status'            => 'Agendada',
            ]);

            $session->students()->sync($data['student_ids']);

            $this->sendSessionEmails($session, "Nova Sessão Agendada", "Uma nova sessão foi registrada.");

            return $session;
        });
    }

    public function cancel(Session $session, string $reason): Session
    {
        $session->update([
            'status' => 'Cancelada', 
            'cancellation_reason' => $reason
        ]);

        $this->sendSessionEmails(
            $session,
            "Sessão Cancelada",
            "Informamos que a sua sessão foi cancelada. Motivo: {$reason}"
        );

        return $session;
    }



    public function getDailySchedule(int $professionalId, int $studentId, string $date)
    {
        return Session::whereDate('session_date', $date)
            ->where(function ($q) use ($professionalId, $studentId) {
                $q->where('professional_id', $professionalId)
                ->orWhere('student_id', $studentId);
            })
            ->orderBy('start_time')
            ->get(['start_time', 'end_time', 'student_id', 'professional_id']);
    }

    public function getAvailableTimeOptions(): array
    {
        $periods = [
            ['start' => '08:00', 'end' => '12:00'],
            ['start' => '14:00', 'end' => '17:00']
        ];

        $startTimes = [];
        $endTimes = [];

        foreach ($periods as $period) {
            $current = Carbon::parse($period['start']);
            $end = Carbon::parse($period['end']);

            while ($current <= $end) {
                $time = $current->format('H:i');

                // Regra Início: Não pode ser o último horário de cada turno (12h ou 17h)
                if ($time !== '12:00' && $time !== '17:00') {
                    $startTimes[$time] = $time;
                }

                // Regra Fim: Pode ser qualquer um exceto o primeiro de cada turno
                if ($time !== '08:00' && $time !== '14:00') {
                    $endTimes[$time] = $time;
                }

                $current->addMinutes(30);
            }
        }

        return [
            'start' => $startTimes,
            'end' => $endTimes
        ];
    }


    // mostrar somente uma

    public function show(Session $session): Session
    {
        return $session->load(['students.person', 'professional.person']);
    }
    
    /**
     * Busca todas as sessões de um aluno específico com relacionamentos
     */
    public function getSessionsByStudent(int $studentId)
    {
        return Session::whereHas('students', function($q) use ($studentId) {
                $q->where('students.id', $studentId);
            })
            ->with(['professional.person', 'students.person', 'sessionRecord'])
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();
    }

    /**
     * Busca todas as sessões de um profissional específico
     */
    public function getSessionsByProfessional(int $professionalId)
    {
        return Session::where('professional_id', $professionalId)
            ->with(['student.person', 'sessionRecord'])
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();
    }

    //atualizar

    public function update(Session $session, array $data): Session
    {
        return DB::transaction(function () use ($session, $data) {
            // 1. Normaliza o tempo (gera o end_time se estiver vazio)
            $this->normalizeTime($data);

            // 2. Prepara os dados para a detecção de conflito.
            // Como o formulário de edit não envia student_id/professional_id (são hidden ou fixos),
            // nós garantimos que a verificação use os IDs atuais da sessão.
            $dataForConflict = array_merge($data, [
                'student_id' => $session->student_id,
                'professional_id' => $session->professional_id,
                'session_date' => $data['session_date'] ?? $session->session_date
            ]);

            // 3. Detecta conflito IGNORANDO o ID da própria sessão
            $conflict = $this->detectConflict($dataForConflict, $session->id);

            if ($conflict['hasConflict']) {
                $errors = [];
                if ($conflict['student']) $errors['session_date'] = "O aluno já possui sessão neste horário.";
                if ($conflict['professional']) $errors['session_date'] = "O profissional já possui sessão neste horário.";
                
                throw ValidationException::withMessages($errors);
            }

            // 4. Atualiza e envia e-mail
            $session->update($data);
            $this->sendSessionEmails($session, "Sessão de Atendimento Atualizada", "Houve uma alteração nos detalhes da sua sessão.");

            return $session;
        });
    }
 
    //soft delete

    public function delete(Session $session): void
    {
        // Mensagem específica solicitada para exclusão
        $this->sendSessionEmails(
            $session, 
            "Registro de Sessão Removido", 
            "Estamos excluindo seu registro de sessão, ela foi cancelada."
        );
        
        $session->delete();
    }

    //restaurar

    public function restore(Session $session): Session
    {
        $session->restore();

        return $session;
    }

    //excluir definitivamente

    public function forceDelete(Session $session): void
    {
        $session->forceDelete();
    }
}
