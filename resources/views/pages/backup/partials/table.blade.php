<x-table.table :headers="['Arquivo ZIP', 'Status', 'Tamanho', 'Criado em', 'Responsável', 'Ações']" :records="$backups">
    @forelse($backups as $backup)
        <tr>
            {{-- ARQUIVO ZIP --}}
            <x-table.td>
                <a href="{{ route('backup.backups.show', $backup->id) }}" class="fw-medium text-decoration-none hover-underline text-dark">
                    <i class="fas fa-file-archive text-warning me-1"></i>
                    {{ $backup->file_name }}
                </a>
            </x-table.td>

            {{-- STATUS (Estilo TA: Texto colorido e negrito) --}}
            <x-table.td>
                @php
                    $statusConfig = [
                        'success'  => 'success',
                        'failed'   => 'danger',
                        'archived' => 'info',
                    ];
                    $color = $statusConfig[$backup->status] ?? 'secondary';
                    $label = [
                        'success'  => 'Sucesso',
                        'failed'   => 'Falha',
                        'archived' => 'Arquivado',
                    ][$backup->status] ?? $backup->status;
                @endphp

                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}-emphasis border px-2">
                    {{ $label }}
                </span>
            </x-table.td>

            {{-- TAMANHO --}}
            <x-table.td>
                <span class="text-primary fw-medium">{{ $backup->size }}</span>
            </x-table.td>

            {{-- CRIADO EM --}}
            <x-table.td>
                <span class="text-muted small">
                    {{ $backup->created_at->format('d/m/Y H:i') }}
                </span>
            </x-table.td>

            {{-- RESPONSÁVEL --}}
            <x-table.td>
                <span class="text-secondary fw-medium">
                    {{ $backup->user->name ?? 'Sistema' }}
                </span>
            </x-table.td>

            <x-table.td>
                <x-table.actions>
                    @canany(['backup.download', 'backup.restore', 'backup.show', 'backup.destroy'])
                         @can('backup.download')
                            <x-buttons.link-button
                                :href="route('backup.backups.download', $backup->id)"
                                variant="success"
                            >
                                <i class="fas fa-download"></i> Baixar
                            </x-buttons.link-button>
                        @endcan

                        @can('backup.restore')
                            @if($backup->status === 'success')
                                <form action="{{ route('backup.backups.restore', $backup->id) }}"
                                      method="POST"
                                      class="d-inline form-restore">
                                    @csrf
                                    <x-buttons.submit-button
                                        variant="warning"
                                        class="btn-restore"
                                        data-filename="{{ $backup->file_name }}"
                                    >
                                        <i class="fas fa-history"></i> Restaurar
                                    </x-buttons.submit-button>
                                </form>
                            @endif
                        @endcan

                        @can('backup.show')
                            <x-buttons.link-button
                                :href="route('backup.backups.show', $backup->id)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('backup.destroy')
                            <form action="{{ route('backup.backups.destroy', $backup->id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <x-buttons.submit-button
                                    variant="danger"
                                    onclick="return confirm('Deseja remover este backup?')"
                                >
                                    <i class="fas fa-trash-alt"></i> Excluir
                                </x-buttons.submit-button>
                            </form>
                        @endcan
                    @else
                        <span class="text-purple-light">Nenhuma ação</span>
                    @endcanany
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">Nenhum backup encontrado no histórico.</td>
        </tr>
    @endforelse
</x-table.table>

@if($backups->hasPages())
    <div class="mt-3">
        {{ $backups->links() }}
    </div>
@endif
