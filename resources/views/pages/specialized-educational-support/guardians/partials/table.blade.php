<x-table.table :headers="['Nome', 'Documento', 'Vínculo (Parentesco)', 'Contato', 'Ações']">
        @forelse($guardians as $guardian)
            <tr>
                <x-table.td>
                   {{ $guardian->person->name }}
                </x-table.td>

                <x-table.td>
                    {{ $guardian->person->document }}
                </x-table.td>

                <x-table.td>
                        {{ ucfirst($guardian->relationship) }}
                </x-table.td>

                <x-table.td>
                        {{ $guardian->person->email }}
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.guardians.show', $guardian)"
                            variant="info"
                        >
                            <i class="fas fa-eye"></i> ver
                        </x-buttons.link-button>

                        <form action="{{ route('specialized-educational-support.guardians.destroy', [$student, $guardian]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja remover este vínculo de responsabilidade?')"
                            >
                                <i class="fas fa-trash"></i> Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">Nenhum responsável cadastrado para este aluno.</td>
            </tr>
        @endforelse
    </x-table.table>