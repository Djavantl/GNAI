<x-table.table :headers="['Barreira', 'Categoria', 'Prioridade', 'Status Atual', 'Data Ident.', 'Ações']" :records="$barriers">
    @forelse($barriers as $barrier)
        @php
            $latestStage = $barrier->latestStage();
            $status = $barrier->currentStatus();
            $nextStep = $latestStage ? $latestStage->step_number + 1 : 1;

            // Lógica para definir o botão de ação principal baseado na etapa
            $actionRoute = null;
            $actionLabel = '';
            $actionIcon = '';
            $actionVariant = 'primary';

            if (!$barrier->isClosedOrNotApplicable()) {
                $actionRoute = route("inclusive-radar.barriers.stage{$nextStep}", $barrier);
                $actionLabel = match($nextStep) {
                    2 => 'Analisar',
                    3 => 'Planejar',
                    4 => 'Resolver',
                    default => 'Avançar'
                };
                $actionIcon = match($nextStep) {
                    2 => 'fa-microscope',
                    3 => 'fa-clipboard-list',
                    4 => 'fa-check-double',
                    default => 'fa-arrow-right'
                };
            }
        @endphp

        <tr>
            {{-- BARREIRA --}}
            <x-table.td>
                <span class="fw-bold text-dark">{{ $barrier->name }}</span>
                <br><small class="text-muted">{{ $barrier->institution?->name }}</small>
            </x-table.td>

            {{-- CATEGORIA --}}
            <x-table.td>{{ $barrier->category?->name ?? 'Não definida' }}</x-table.td>

            {{-- PRIORIDADE --}}
            <x-table.td>
                <span class="badge bg-{{ $barrier->priority->color() }}">
                    {{ $barrier->priority->label() }}
                </span>
            </x-table.td>

            {{-- STATUS ATUAL --}}
            <x-table.td>
                @if($status)
                    <span class="text-{{ $status->color() }} fw-bold">
                        <i class="fas fa-circle fs-xs me-1"></i>
                        {{ $status->label() }}
                    </span>
                @else
                    <span class="text-muted">Sem status</span>
                @endif
            </x-table.td>

            {{-- DATA IDENT. --}}
            <x-table.td>{{ $barrier->identified_at->format('d/m/Y') }}</x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    {{-- Botão de Ver Sempre Disponível --}}
                    <x-buttons.link-button
                        :href="route('inclusive-radar.barriers.show', $barrier)"
                        variant="info"
                        title="Ver Detalhes/Linha do Tempo"
                    >
                        <i class="fas fa-eye"></i>
                    </x-buttons.link-button>

                    {{-- Botão Dinâmico de Próxima Etapa --}}
                    @if($actionRoute)
                        <x-buttons.link-button
                            :href="$actionRoute"
                            variant="{{ $actionVariant }}"
                            title="{{ $actionLabel }}"
                        >
                            <i class="fas {{ $actionIcon }}"></i> {{ $actionLabel }}
                        </x-buttons.link-button>
                    @endif

                    {{-- Exclusão --}}
                    <form action="{{ route('inclusive-radar.barriers.destroy', $barrier) }}"
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja remover este registro de barreira?')"
                        >
                            <i class="fas fa-trash-alt"></i>
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">Nenhuma barreira encontrada.</td>
        </tr>
    @endforelse
</x-table.table>
