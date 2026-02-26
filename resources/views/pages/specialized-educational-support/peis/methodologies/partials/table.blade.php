<x-table.table :headers="['Título', 'Descrição', 'Ações']" aria-label="Tabela de metodologias do PEI">
        @forelse($pei->methodologies as $methodology)
            <tr>
                {{-- título --}}
                <x-table.td class="w-50 align-middle text-start">
                    {{ $methodology->title }}
                </x-table.td>

                {{-- descrição --}}
                <x-table.td class="w-25 align-middle text-start">
                    {{ $methodology->description ?? '---' }}
                </x-table.td>

                {{-- ações --}}
                <x-table.td class="w-25 align-middle text-end">
                    
                    <x-table.actions class="d-flex justify-content-center gap-2">
                        <x-buttons.link-button
                            href="{{ route('specialized-educational-support.pei.methodology.show', $methodology) }}"
                            variant="info"
                            aria-label="Ver metodologia">
                            <i class="fas fa-eye" aria-hidden="true"></i> Ver
                        </x-buttons.link-button>
                    </x-table.actions>
                    
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-muted py-5">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size: 2rem;"></i>
                    Nenhuma metodologia cadastrada nesse PEI.
                </td>
            </tr>
        @endforelse
    </x-table.table>