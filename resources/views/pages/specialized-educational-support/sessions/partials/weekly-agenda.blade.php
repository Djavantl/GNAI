@php
    $statusStyles = [
        'agendada' => ['warning', 'Agendada'],
        'scheduled' => ['warning', 'Agendada'],
        'realizada' => ['success', 'Realizada'],
        'completed' => ['success', 'Realizada'],
        'cancelada' => ['danger', 'Cancelada'],
        'cancelled' => ['danger', 'Cancelada'],
        'canceled' => ['danger', 'Cancelada'],
    ];

    $totalSessions = collect($agenda['days'] ?? [])
        ->sum(fn ($day) => $day['sessions']->count());
@endphp

<div class="weekly-agenda">
    <div class="alert alert-light border mb-4">
        <strong>Período:</strong> {{ $agenda['weekStart']->format('d/m/Y') }}
        até {{ $agenda['weekEnd']->format('d/m/Y') }}
        <br>
        <strong>Total de agendamentos na semana:</strong> {{ $totalSessions }}
    </div>

    <div class="row g-3">
        @foreach($agenda['days'] as $dayIndex => $day)
            <div class="col-12 col-lg-6 col-xxl-3">
                <div class="card h-100 border shadow-sm weekly-day-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold weekly-day-title">{{ $day['label'] }}</div>
                            <small class="weekly-day-date">{{ $day['date']->format('d/m/Y') }}</small>
                        </div>

                        <span class="badge bg-secondary">
                            {{ $day['sessions']->count() }}
                        </span>
                    </div>

                    <div class="card-body p-2">
                        <div class="d-flex flex-column gap-2 weekly-sessions-list">
                            @forelse($day['sessions'] as $session)
                                @php
                                    $statusKey = strtolower($session->status ?? '');
                                    $statusData = $statusStyles[$statusKey] ?? ['secondary', $session->statusLabel()];

                                    $studentsText = $session->students
                                        ->map(fn ($student) => $student->person->name ?? 'Aluno')
                                        ->implode(', ');

                                    $professionalName = $session->professional->person->name ?? 'Profissional';

                                    $hasRecord = $session->isPedagogicalAttendance()
                                        ? (bool) $session->pedagogicalRecord
                                        : (bool) $session->sessionRecord;
                                    $recordLabel = $hasRecord
                                        ? ($session->isPedagogicalAttendance() ? 'Atendimento Pedagógico registrado' : 'Atendimento AEE registrado')
                                        : 'Sem registro';
                                    $recordClass = $hasRecord
                                        ? 'weekly-session-record weekly-session-record--done'
                                        : 'weekly-session-record weekly-session-record--missing';

                                    $startTime = \Carbon\Carbon::parse($session->start_time)->format('H:i');
                                    $endTime = \Carbon\Carbon::parse($session->end_time)->format('H:i');
                                @endphp

                                <div class="border rounded-3 p-2 bg-light d-flex flex-column h-100 weekly-session-card">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <div class="fw-bold small weekly-session-time">
                                                {{ $startTime }} - {{ $endTime }}
                                            </div>
                                        </div>

                                        <span class="badge bg-{{ $statusData[0] }}">
                                            {{ $statusData[1] }}
                                        </span>
                                    </div>

                                    <div class="mt-2 small">
                                        <div class="weekly-session-professional">
                                            <strong>Profissional:</strong> {{ $professionalName }}
                                        </div>

                                        <div class="weekly-session-students">
                                            <strong>Aluno(s):</strong> {{ $studentsText ?: 'Sem alunos' }}
                                        </div>

                                        <div class="{{ $recordClass }}">
                                            {{ $recordLabel }}
                                        </div>
                                    </div>

                                    <div class="mt-auto pt-2 weekly-session-actions">
                                        @can('session.view')
                                            <x-buttons.link-button
                                                :href="route('specialized-educational-support.sessions.show', $session)"
                                                variant="info"
                                            >
                                                <i class="fas fa-eye"></i> Ver
                                            </x-buttons.link-button>
                                        @endcan
                                    </div>
                                </div>
                            @empty
                                <div class="text-center weekly-empty-state py-4">
                                    <i class="fas fa-calendar-day d-block mb-2" style="font-size: 2rem;"></i>
                                    Nenhum agendamento neste dia.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
