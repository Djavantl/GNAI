<x-table.table :headers="['Título', 'Descrição', 'Ações']" aria-label="Tabela de conteúdo programático do PEI">
        @forelse($pei->contentProgrammatic as $content)
            <tr>
                {{-- título: ocupa metade da linha --}}
                <x-table.td class="w-50 align-middle text-start">
                    {{ $content->title }}
                </x-table.td>

                {{-- descrição: coluna central --}}
                <x-table.td class="w-25 align-middle text-start">
                    {{ $content->description ?? '---' }}
                </x-table.td>

                {{-- ações: à direita --}}
                <x-table.td class="w-25 align-middle text-end">
                    
                    <x-table.actions class="d-flex justify-content-center gap-2">
                        <x-buttons.link-button
                            href="{{ route('specialized-educational-support.pei.content.show', $content) }}"
                            variant="info"
                            aria-label="Ver conteúdo">
                            <i class="fas fa-eye" aria-hidden="true"></i> Ver
                        </x-buttons.link-button>
                    </x-table.actions>
                   
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-muted py-5">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size: 2rem;"></i>
                    Nenhum conteúdo específico cadastrado nesse PEI.
                </td>
            </tr>
        @endforelse
    </x-table.table>