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

<div class="alert alert-light border mb-4">
    <strong>Período:</strong> {{ $agenda['weekStart']->format('d/m/Y') }}
    até {{ $agenda['weekEnd']->format('d/m/Y') }}
    <br>
    <strong>Total de sessões na semana:</strong> {{ $totalSessions }}
</div>

<div class="row g-3">
    @foreach($agenda['days'] as $dayIndex => $day)
        <div class="col-12 col-lg-6 col-xxl-3">
            <div class="card h-100 border shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold">{{ $day['label'] }}</div>
                        <small class="text-muted">{{ $day['date']->format('d/m/Y') }}</small>
                    </div>

                    <span class="badge bg-secondary">
                        {{ $day['sessions']->count() }}
                    </span>
                </div>

                <div class="card-body">
                    @forelse($day['sessions'] as $sessionIndex => $session)
                        @php
                            $statusKey = strtolower($session->status ?? '');
                            $statusData = $statusStyles[$statusKey] ?? ['secondary', ucfirst($session->status ?? 'Sem status')];

                            $studentsText = $session->students
                                ->map(fn ($student) => $student->person->name ?? 'Aluno')
                                ->implode(', ');

                            $professionalName = $session->professional->person->name ?? 'Profissional';

                            $typeLabel = match (strtolower($session->type ?? '')) {
                                'individual' => 'Individual',
                                'group' => 'Grupo',
                                default => ucfirst($session->type ?? 'Sem tipo'),
                            };
                        @endphp

                        <div
                            class="border rounded-3 p-3 bg-light {{ $sessionIndex === 0 ? '' : 'd-none' }}"
                            data-weekly-session-card="{{ $dayIndex }}"
                            data-session-index="{{ $sessionIndex }}"
                        >
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fw-bold">
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                    </div>
                                    <small class="text-muted">Sessão #{{ $session->id }}</small>
                                </div>

                                <span class="badge bg-{{ $statusData[0] }}">
                                    {{ $statusData[1] }}
                                </span>
                            </div>

                            <div class="mt-2 small">
                                <div><strong>Tipo:</strong> {{ $typeLabel }}</div>
                                <div><strong>Profissional:</strong> {{ $professionalName }}</div>
                                <div><strong>Alunos:</strong> {{ $studentsText ?: 'Sem alunos' }}</div>
                                <div>
                                    <strong>Atendimento:</strong>
                                    {{ $session->sessionRecord ? 'Registro disponível' : 'Sem registro ainda' }}
                                </div>
                            </div>

                            <div class="mt-3 d-flex gap-2 flex-wrap">
                                <x-buttons.link-button
                                    :href="route('specialized-educational-support.sessions.show', $session)"
                                    variant="info"
                                >
                                    <i class="fas fa-eye"></i> Ver
                                </x-buttons.link-button>

                                <x-buttons.link-button
                                    :href="route('specialized-educational-support.sessions.edit', $session)"
                                    variant="warning"
                                >
                                    <i class="fas fa-pen"></i> Editar
                                </x-buttons.link-button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-calendar-day d-block mb-2" style="font-size: 2rem;"></i>
                            Nenhuma sessão neste dia.
                        </div>
                    @endforelse

                    @if($day['sessions']->count() > 1)
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                data-weekly-prev="{{ $dayIndex }}"
                            >
                                <i class="fas fa-chevron-left"></i>
                            </button>

                            <small class="text-muted" data-weekly-counter="{{ $dayIndex }}">
                                1 / {{ $day['sessions']->count() }}
                            </small>

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                data-weekly-next="{{ $dayIndex }}"
                            >
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dayCards = document.querySelectorAll('[data-weekly-prev]');

    dayCards.forEach(function (prevButton) {
        const dayIndex = prevButton.getAttribute('data-weekly-prev');
        const nextButton = document.querySelector('[data-weekly-next="' + dayIndex + '"]');
        const counter = document.querySelector('[data-weekly-counter="' + dayIndex + '"]');
        const cards = Array.from(document.querySelectorAll('[data-weekly-session-card="' + dayIndex + '"]'));

        if (!cards.length || !nextButton || !counter) return;

        let currentIndex = 0;

        function updateView() {
            cards.forEach((card, index) => {
                card.classList.toggle('d-none', index !== currentIndex);
            });

            counter.textContent = (currentIndex + 1) + ' / ' + cards.length;
        }

        prevButton.addEventListener('click', function () {
            currentIndex = (currentIndex - 1 + cards.length) % cards.length;
            updateView();
        });

        nextButton.addEventListener('click', function () {
            currentIndex = (currentIndex + 1) % cards.length;
            updateView();
        });

        updateView();
    });
});
</script>
@endpush