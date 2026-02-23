{{-- Sessões de Atendimento --}}
<section id="sessions" class="mb-5 bg-white rounded shadow-sm border">

    <x-forms.section title="Sessões de Atendimento Especializado" class="m-0" />

    <div class="p-3">
        <div class="table-responsive">
            <x-table.table :headers="['Data', 'Profissional', 'Tipo', 'Status', 'Ações']">

                {{-- Mostramos apenas as 5 mais recentes --}}
                @forelse($student->sessions->take(5) as $session)
                    <tr>
                        {{-- DATA --}}
                        <x-table.td>
                            <span class="fw-bold">
                                {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}
                            </span>
                            <br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - 
                                {{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '--:--' }}
                            </small>
                        </x-table.td>

                        {{-- PROFISSIONAL --}}
                        <x-table.td>
                            {{ $session->professional->person->name }}
                        </x-table.td>

                        {{-- TIPO --}}
                        <x-table.td>
                            <span class="badge bg-light text-dark border">
                                {{ $session->type === 'group' ? 'Grupo' : 'Individual' }}
                            </span>
                        </x-table.td>

                        {{-- STATUS (Unificado com suas strings de banco) --}}
                        <x-table.td>
                            @php
                                $statusValue = strtolower($session->status);
                                $statusStyle = match($statusValue) {
                                    'scheduled', 'agendado' => ['bg' => 'bg-info', 'icon' => 'fa-calendar-alt', 'label' => 'Agendado'],
                                    'completed', 'realizado' => ['bg' => 'bg-success', 'icon' => 'fa-check-circle', 'label' => 'Realizado'],
                                    'canceled', 'cancelled', 'cancelado' => ['bg' => 'bg-danger', 'icon' => 'fa-times-circle', 'label' => 'Cancelado'],
                                    default => ['bg' => 'bg-secondary', 'icon' => 'fa-clock', 'label' => $session->status]
                                };
                            @endphp
                            <span class="badge {{ $statusStyle['bg'] }}">
                                <i class="fas {{ $statusStyle['icon'] }} me-1"></i> {{ $statusStyle['label'] }}
                            </span>
                        </x-table.td>

                        {{-- AÇÕES RÁPIDAS --}}
                        <x-table.td>
                            <x-table.actions>
                                <x-buttons.link-button
                                    :href="route('specialized-educational-support.sessions.show', $session->id)"
                                    variant="info"
                                    class="btn-sm"
                                    title="Ver Detalhes"
                                >
                                    <i class="fas fa-eye"></i>
                                </x-buttons.link-button>
                                
                                @if($session->sessionRecord)
                                    <x-buttons.link-button
                                        :href="route('specialized-educational-support.session-records.show', $session->sessionRecord->id)"
                                        variant="dark"
                                        class="btn-sm"
                                        title="Ver Registro"
                                    >
                                        <i class="fas fa-file-alt"></i>
                                    </x-buttons.link-button>
                                @endif
                            </x-table.actions>
                        </x-table.td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            Nenhuma sessão registrada para este aluno.
                        </td>
                    </tr>
                @endforelse

            </x-table.table>
        </div>

        {{-- GESTÃO (Link para Index e Create Específico) --}}
        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <p class="text-muted small m-0">Exibindo as últimas 5 sessões.</p>
            <div class="d-flex gap-2">
                <x-buttons.link-button
                    :href="route('specialized-educational-support.students.sessions.index', $student->id)"
                    variant="warning"
                    class="btn-sm">
                    <i class="fas fa-folder-open"></i> Gerenciar Sessões
                </x-buttons.link-button>
            </div>
        </div>

    </div>
</section>