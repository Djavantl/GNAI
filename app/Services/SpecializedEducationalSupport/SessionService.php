<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Mail\SessionNotification;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Session;
use App\Models\SpecializedEducationalSupport\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class SessionService
{
    public function index(array $filters = [])
    {
        return Session::query()
            ->with(['students.person', 'professional.person', 'sessionRecord'])
            ->student($filters['student'] ?? null)
            ->professional($filters['professional'] ?? null)
            ->type($filters['type'] ?? null)
            ->status($filters['status'] ?? null)
            ->orderByDesc('session_date')
            ->paginate(10)
            ->withQueryString();
    }

    public function getMySessions(array $filters = [])
    {
        $professional = Auth::user()?->professional;

        if (!$professional) {
            abort(403, 'Acesso permitido apenas para profissionais.');
        }

        return Session::query()
            ->with([
                'students.person',
                'professional.person',
                'sessionRecord'
            ])
            ->where('professional_id', $professional->id)
            ->student($filters['student'] ?? null)
            ->type($filters['type'] ?? null)
            ->status($filters['status'] ?? null)
            ->orderByDesc('session_date')
            ->paginate(10)
            ->withQueryString();
    }

    public function getWeeklyAgenda(array $filters = []): array
    {
        return $this->buildWeeklyAgenda($filters);
    }

    public function getMyWeeklyAgenda(array $filters = []): array
    {
        $professional = Auth::user()?->professional;

        if (!$professional) {
            abort(403, 'Acesso permitido apenas para profissionais.');
        }

        return $this->buildWeeklyAgenda($filters, $professional->id);
    }

   private function buildWeeklyAgenda(array $filters = [], ?int $fixedProfessionalId = null): array
    {
        $referenceDate = Carbon::parse($filters['week'] ?? now()->toDateString());
        $weekStart = $referenceDate->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weekEnd = $referenceDate->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        // 1. Buscar sessões aplicando filtros de período E filtros de usuário
        $sessions = Session::query()
            ->with(['students.person', 'professional.person', 'sessionRecord'])
            ->whereBetween('session_date', [$weekStart, $weekEnd])
            // Aplica filtro de profissional se vier do "MySessions" ou do filtro da tela
            ->where(function($q) use ($filters, $fixedProfessionalId) {
                if ($fixedProfessionalId) {
                    $q->where('professional_id', $fixedProfessionalId);
                } elseif (!empty($filters['professional'])) {
                    $q->where('professional_id', $filters['professional']);
                }
            })
            // Aplica filtro de aluno se selecionado
            ->when($filters['student'] ?? null, function($q, $studentId) {
                $q->whereHas('students', function($sq) use ($studentId) {
                    $sq->where('students.id', $studentId);
                });
            })
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        // 2. Montar estrutura da agenda por dias
        $days = [];
        $currentDate = $weekStart->copy();

        while ($currentDate <= $weekEnd) {
            $dateString = $currentDate->toDateString();
            
            $days[] = [
                'date'     => $currentDate->copy(),
                'label'    => $this->getTranslatedDayName($currentDate),
                'sessions' => $sessions->filter(fn($s) => $s->session_date->toDateString() === $dateString)
            ];

            $currentDate->addDay();
        }

        return [
            'weekStart' => $weekStart,
            'weekEnd'   => $weekEnd,
            'days'      => $days
        ];
    }

    private function getTranslatedDayName(Carbon $date): string
    {
        $days = [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
        ];
        return $days[$date->dayOfWeek];
    }

    private function sendSessionEmails(Session $session, string $subject, string $text)
    {
        $session->load(['students.person', 'professional.person']);

        $emails = [];

        foreach ($session->students as $student) {
            if ($student->person->email) {
                $emails[] = $student->person->email;
            }
        }

        if ($session->professional?->person?->email) {
            $emails[] = $session->professional->person->email;
        }

        $emails = array_values(array_unique($emails));

        foreach ($emails as $email) {
            Mail::to($email)->send(
                new SessionNotification($session, $subject, $text)
            );
        }
    }

    private function normalizeTime(array &$data): void
    {
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
        $start = Carbon::parse($data['start_time'])->format('H:i:00');
        $end = Carbon::parse($data['end_time'])->format('H:i:00');

        $baseQuery = Session::whereDate('session_date', $date)
            // ADICIONE ESTA LINHA: Ignora sessões canceladas na checagem de conflito
            ->where('status', '!=', 'Cancelada') 
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where(function ($query) use ($start, $end) {
                $query->whereTime('start_time', '<', $end)
                    ->whereTime('end_time', '>', $start);
            });

        $professionalConflict = (clone $baseQuery)
            ->where('professional_id', $data['professional_id'])
            ->exists();

        $studentIds = $data['student_ids'] ?? [];

        $conflictingStudents = (clone $baseQuery)
            ->whereHas('students', function ($q) use ($studentIds) {
                $q->whereIn('students.id', $studentIds);
            })
            ->with('students.person')
            ->get()
            ->pluck('students')
            ->flatten()
            ->filter(fn ($s) => in_array($s->id, $studentIds))
            ->mapWithKeys(fn ($s) => [$s->id => $s->person->name])
            ->toArray();

        return [
            'students' => $conflictingStudents,
            'professional' => $professionalConflict,
            'hasConflict' => $professionalConflict || !empty($conflictingStudents),
        ];
    }

    public function create(array $data): Session
    {
        return DB::transaction(function () use ($data) {
            $this->ensureProfessionalIsActive($data['professional_id']);
            $this->ensureStudentsAreActive($data['student_ids']);

            $this->normalizeTime($data);
            $conflict = $this->detectConflict($data);

            if ($conflict['hasConflict']) {
                $errors = [];

                if (!empty($conflict['students'])) {
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

            $this->sendSessionEmails(
                $session,
                "Nova Sessão Agendada",
                "Uma nova sessão foi registrada."
            );

            return $session->fresh(['students.person', 'professional.person', 'sessionRecord']);
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

    public function show(Session $session): Session
    {
        return $session->load(['students.person', 'professional.person', 'sessionRecord']);
    }

    public function update(Session $session, array $data): Session
    {
        return DB::transaction(function () use ($session, $data) {
            $professionalId = $data['professional_id'] ?? $session->professional_id;
            $this->ensureProfessionalIsActive($professionalId);

            $studentIds = $data['student_ids'] ?? $session->students()->pluck('students.id')->toArray();
            $this->ensureStudentsAreActive($studentIds);

            $data = array_merge($data, [
                'professional_id' => $professionalId,
                'student_ids' => $studentIds,
                'session_date' => $data['session_date'] ?? optional($session->session_date)->format('Y-m-d'),
                'start_time' => $data['start_time'] ?? Carbon::parse($session->start_time)->format('H:i'),
                'end_time' => $data['end_time'] ?? Carbon::parse($session->end_time)->format('H:i'),
            ]);

            $this->normalizeTime($data);

            $conflict = $this->detectConflict($data, $session->id);

            if ($conflict['hasConflict']) {
                $errors = [];

                if (!empty($conflict['students'])) {
                    $names = implode(', ', $conflict['students']);
                    $errors['student_ids'] = "Conflito de agenda para: {$names}.";
                }

                if ($conflict['professional']) {
                    $errors['professional_id'] = "O profissional já possui uma sessão neste horário.";
                }

                throw ValidationException::withMessages($errors);
            }

            $session->update([
                'professional_id' => $professionalId,
                'session_date' => $data['session_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'type' => $data['type'] ?? $session->type,
                'location' => $data['location'] ?? $session->location,
                'session_objective' => $data['session_objective'] ?? $session->session_objective,
                'status' => $data['status'] ?? $session->status,
            ]);

            $session->students()->sync($studentIds);

            $this->sendSessionEmails(
                $session,
                "Sessão de Atendimento Atualizada",
                "Houve uma alteração nos detalhes da sua sessão."
            );

            return $session->fresh(['students.person', 'professional.person', 'sessionRecord']);
        });
    }

    public function delete(Session $session): void
    {
        DB::transaction(function () use ($session) {

            if ($session->status === 'Agendada') {

                $this->cancel(
                    $session,
                    'Sessão cancelada automaticamente devido à exclusão do registro.'
                );
            }

            $session->delete();
        });
    }

    public function restore(Session $session): Session
    {
        $session->restore();

        return $session;
    }

    public function forceDelete(Session $session): void
    {
        $session->forceDelete();
    }

    private function ensureStudentsAreActive(array $studentIds): void
    {
        $inactiveStudents = Student::whereIn('id', $studentIds)
            ->where('status', '!=', 'active')
            ->with('person')
            ->get()
            ->pluck('person.name')
            ->toArray();

        if (!empty($inactiveStudents)) {
            $names = implode(', ', $inactiveStudents);

            throw ValidationException::withMessages([
                'student_ids' => "Não é possível agendar ou editar sessão para aluno inativo: {$names}."
            ]);
        }
    }

    private function ensureProfessionalIsActive(int $professionalId): void
    {
        $professional = Professional::with('person')->find($professionalId);

        if (!$professional) {
            throw ValidationException::withMessages([
                'professional_id' => 'Profissional não encontrado.'
            ]);
        }

        if ($professional->status !== 'active') {
            $name = $professional->person->name ?? 'Profissional';

            throw ValidationException::withMessages([
                'professional_id' => "Não é possível agendar ou editar sessão para profissional inativo: {$name}."
            ]);
        }
    }

    public function getDailySchedule(int $professionalId, int $studentId, string $date)
    {
        return Session::whereDate('session_date', $date)
            ->where(function ($q) use ($professionalId, $studentId) {
                $q->where('professional_id', $professionalId)
                    ->orWhereHas('students', function ($sq) use ($studentId) {
                        $sq->where('students.id', $studentId);
                    });
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

                if ($time !== '12:00' && $time !== '17:00') {
                    $startTimes[$time] = $time;
                }

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
}