{{-- REGISTROS DE SESSÕES --}}
<section id="session-records" class="mb-5 rounded shadow-sm">

    <x-forms.section title="Registros de Atendimentos AEE" class="m-0" />

    <div class="pb-3 ps-3 pe-3">

        <div class="table-responsive">
            <x-table.table :headers="[
                ['label' => 'Data', 'responsive' => false],
                'Profissional',
                'Duração',
                'Presença',
                ['label' => '', 'responsive' => false],
            ]">

                @forelse($sessionEvaluations as $evaluation)
                    @php
                        $sessionRecord = $evaluation->sessionRecord;
                        $attendanceSession = $sessionRecord?->attendanceSession;
                        $professional = $attendanceSession?->professional;
                    @endphp

                    <tr>

                        {{-- DATA --}}
                        <x-table.td :responsive="false">
                            <span class="fw-bold text-purple-dark">
                                {{ $attendanceSession?->session_date?->format('d/m/Y') ?? '—' }}
                            </span>
                        </x-table.td>

                        {{-- PROFISSIONAL --}}
                        <x-table.td>
                            {{ $professional?->person?->name ?? 'Não informado' }}
                        </x-table.td>

                        {{-- DURAÇÃO --}}
                        <x-table.td>
                            {{ $sessionRecord?->duration ?? '—' }}
                        </x-table.td>

                        {{-- PRESENÇA --}}
                        <x-table.td>
                            @if($evaluation->is_present)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i> Presente
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i> Ausente
                                </span>
                            @endif
                        </x-table.td>

                        

                        {{-- AÇÕES --}}
                        <x-table.td :responsive="false">
                            <x-table.actions>
                                @can('session-record.view')
                                    <x-buttons.link-button
                                        :href="route('specialized-educational-support.students.session-records.show', [$student, $evaluation])"
                                        variant="info"
                                        class="btn-sm"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </x-buttons.link-button>
                                @endcan
                            </x-table.actions>
                        </x-table.td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted fw-bold py-5">
                            <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
                            Nenhum registro de atendimento AEE encontrado.
                        </td>
                    </tr>
                @endforelse

            </x-table.table>
        </div>

        <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
            @canany(['session-record.view-all', 'session-record.view-own'])
                <x-buttons.link-button
                    :href="route('specialized-educational-support.students.session-records.index', $student)"
                    variant="warning"
                    class="btn-sm">
                    <i class="fas fa-folder-open"></i> Gerenciar Registros
                </x-buttons.link-button>
            @endcanany
        </div>

        <div class="mt-3">
            {{ $sessionEvaluations->links() }}
        </div>

    </div>
</section>
