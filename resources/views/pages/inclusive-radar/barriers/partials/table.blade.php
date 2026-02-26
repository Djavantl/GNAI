<x-table.table :headers="['Barreira', 'Categoria', 'Prioridade', 'Status Atual', 'Data Ident.', 'Ações']" :records="$barriers">

    @forelse($barriers as $barrier)

        @php
            $status = $barrier->currentStatus();

            // Se a barreira for não aplicável ou já concluída, não há próxima etapa
            $nextStep = $barrier->isClosedOrNotApplicable() ? null : $barrier->nextStep();

            $actionRoute = null;
            $actionLabel = '';
            $actionIcon = '';

            if ($nextStep) {
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
                <br>
                <small class="text-muted">
                    {{ $barrier->institution?->name }}
                </small>
            </x-table.td>

            {{-- CATEGORIA --}}
            <x-table.td>
                {{ $barrier->category?->name ?? 'Não definida' }}
            </x-table.td>

            {{-- PRIORIDADE --}}
            <x-table.td>
                <span class="badge bg-{{ $barrier->priority->color() }}">
                    {{ $barrier->priority->label() }}
                </span>
            </x-table.td>

            {{-- STATUS --}}
            <x-table.td>
                @if($status)
                    <span class="text-{{ $status->color() }} fw-bold">
                        {{ $status->label() }}
                    </span>
                @else
                    <span class="text-muted">Sem status</span>
                @endif
            </x-table.td>

            {{-- DATA --}}
            <x-table.td>
                {{ optional($barrier->identified_at)->format('d/m/Y') }}
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    {{-- VER --}}
                    <x-buttons.link-button
                        :href="route('inclusive-radar.barriers.show', $barrier)"
                        variant="info"
                        title="Ver Detalhes"
                    >
                        <i class="fas fa-eye"></i>
                    </x-buttons.link-button>

                    {{-- PRÓXIMA ETAPA --}}
                    @if($actionRoute)
                        <x-buttons.link-button
                            :href="$actionRoute"
                            variant="primary"
                            title="{{ $actionLabel }}"
                        >
                            <i class="fas {{ $actionIcon }}"></i>
                            {{ $actionLabel }}
                        </x-buttons.link-button>
                    @endif

                    {{-- EXCLUIR --}}
                    <form action="{{ route('inclusive-radar.barriers.destroy', $barrier) }}"
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja remover esta barreira?')"
                        >
                            <i class="fas fa-trash-alt"></i>
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>

    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">
                Nenhuma barreira encontrada.
            </td>
        </tr>
    @endforelse

</x-table.table>
