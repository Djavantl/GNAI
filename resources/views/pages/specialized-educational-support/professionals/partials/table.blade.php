<x-table.table 
    :headers="['Nome', 'Email', 'Cargo', 'Status', 'Ações']" 
    :records="$professionals" 
    aria-label="Tabela de profissionais do suporte educacional"
>
@forelse($professionals as $professional)
    <tr>
        <x-table.td>
            <div class="name-with-photo">
                {{-- Importante: Alt vazio em fotos decorativas, ou com o nome se for funcional --}}
                <img src="{{ $professional->person->photo_url }}" 
                     class="avatar-table" 
                     alt="Foto de {{ $professional->person->name }}">
                <span class="fw-bold text-purple-dark">{{ $professional->person->name }}</span>
            </div>
        </x-table.td>
        
        <x-table.td>{{ $professional->person->email }}</x-table.td>
        <x-table.td>{{ $professional->position->name }}</x-table.td>
        
        <x-table.td>
            @php
                $statusColor = $professional->status === 'active' ? 'success' : 'danger';
                $statusLabel = $professional->status === 'active' ? 'Ativo' : 'Inativo';
            @endphp
            
            {{-- Adicionado aria-label para descrever o estado --}}
            <span class="text-{{ $statusColor }} text-uppercase fw-bold" 
                  aria-label="Status: {{ $statusLabel }}">
                {{ $statusLabel }}
            </span>
        </x-table.td>

        <x-table.td>
            <x-table.actions>
                <x-buttons.link-button 
                    :href="route('specialized-educational-support.professionals.show', $professional)"
                    variant="info"
                    aria-label="Visualizar prontuário de {{ $professional->person->name }}"
                >
                <i class="fas fa-eye" aria-hidden="true"></i> ver
                </x-buttons.link-button>

                <form action="{{ route('specialized-educational-support.professionals.destroy', $professional) }}"
                    method="POST"
                    class="d-inline">
                    @csrf
                    @method('DELETE')

                    <x-buttons.submit-button 
                        variant="danger"
                        onclick="return confirm('Deseja remover este profissional?')"
                        aria-label="Excluir profissional {{ $professional->person->name }} do sistema"
                    >
                       <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                    </x-buttons.submit-button>
                </form>
            </x-table.actions>
        </x-table.td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center text-muted fw-bold py-5">
            <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
            Nenhum aluno encontrado.
        </td>
    </tr>
@endforelse
</x-table.table>