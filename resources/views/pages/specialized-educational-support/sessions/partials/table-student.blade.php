 <x-table.table :headers="['Data', 'Profissional', 'Tipo', 'Status', 'Ações']">
    @forelse($sessions as $session)
        <tr>
            <x-table.td>
                <div class="fw-bold">{{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</div>
                <small class="text-muted">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}</small>
            </x-table.td>
            
            <x-table.td>{{ $session->professional->person->name }}</x-table.td>
            
            <x-table.td>
                <span class="badge bg-light text-dark border">
                    {{ $session->type === 'group' ? 'Grupo' : 'Individual' }}
                </span>
            </x-table.td>

            <x-table.td>
                @php
                    $statusValue = strtolower($session->status);
                    $statusColor = match($statusValue) {
                        'scheduled', 'agendado' => 'info',
                        'completed', 'realizado' => 'success',
                        'canceled', 'cancelled', 'cancelado' => 'danger',
                        default => 'warning'
                    };
                @endphp
                <span class="text-{{ $statusColor }} fw-bold">
                    {{ ucfirst($session->status) }}
                </span>
            </x-table.td>

            <x-table.td>
                <x-table.actions>
                    {{-- Ver --}}
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.sessions.show', $session->id)"
                        variant="info"
                    >
                        <i class="fas fa-eye" aria-hidden="true"></i>Ver
                    </x-buttons.link-button>

                    {{-- Excluir --}}
                    <form action="{{ route('specialized-educational-support.sessions.destroy', $session->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Mover esta sessão para a lixeira?')"
                        >
                           <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-5">
                    Nenhuma sessão encontrada para este aluno.
                </td>
            </tr>
    @endforelse
    </x-table.table>