<x-table.table :headers="['Titulo', 'Status', 'Ações']" aria-label="Tabela de objetivos específicos do PEI">
        @forelse($pei->specificObjectives as $obj)
            <tr>
                {{-- descrição: ocupa metade da linha, alinhada à esquerda --}}
                <x-table.td class="w-50 align-middle text-start">
                    {{ $obj->title }}
                </x-table.td>

                {{-- status: coluna central, alinhada verticalmente ao centro e centralizada horizontalmente --}}
                <x-table.td class="w-25 align-middle text-start">
                    @php
                        // se quiser cores diferentes, ajuste conforme os possíveis valores de $obj->status
                        $statusLabel = $obj->status->label();
                        $statusColor = $obj->status->value === 'finished' ? 'success' : ($obj->status->value === 'in_progress' ? 'warning' : 'secondary');
                    @endphp

                    <span class="text-{{ $statusColor }} text-uppercase fw-bold" aria-label="Status: {{ $statusLabel }}">
                        {{ $statusLabel }}
                    </span>
                </x-table.td>

                {{-- ações: sempre à direita --}}
                <x-table.td class="w-25 align-middle text-center">
                  
                    <x-table.actions class="d-flex justify-content-center gap-2">
                        <x-buttons.link-button
                            href="{{ route('specialized-educational-support.pei.objective.show', $obj) }}"
                            variant="info"
                            aria-label="Ver objetivo">
                            <i class="fas fa-eye" aria-hidden="true"></i> Ver
                        </x-buttons.link-button>
                    </x-table.actions>
                    
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-muted py-5">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size: 2rem;"></i>
                    Nenhum objetivo específico cadastrado nesse PEI.
                </td>
            </tr>
        @endforelse
    </x-table.table>