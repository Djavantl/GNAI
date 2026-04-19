<x-table.table
    :headers="[
        ['label' => 'Arquivo ZIP', 'responsive' => false],
        ['label' => 'Status',      'responsive' => true],
        ['label' => 'Tamanho',     'responsive' => true],
        ['label' => 'Criado em',   'responsive' => true],
        ['label' => 'Responsável', 'responsive' => true],
        ['label' => 'Ações',       'responsive' => false],
    ]"
    :records="$backups"
>
    @forelse($backups as $backup)
        <tr>
            <x-table.td :responsive="false">
                <a href="{{ route('backup.backups.show', $backup->id) }}"
                   class="fw-medium text-decoration-none text-dark">
                    <i class="fas fa-file-archive text-warning me-1"></i>
                    {{ $backup->file_name }}
                </a>
            </x-table.td>

            <x-table.td>
                @php
                    $statusMap = [
                        'success'  => ['label' => 'Sucesso',  'color' => 'success'],
                        'failed'   => ['label' => 'Falha',    'color' => 'danger'],
                        'archived' => ['label' => 'Arquivado','color' => 'info'],
                    ];

                    $status = $statusMap[$backup->status] ?? [
                        'label' => $backup->status,
                        'color' => 'secondary'
                    ];
                @endphp

                <span class="text-{{ $status['color'] }} fw-bold text-uppercase"
                      style="font-size: 0.85rem;">
                    {{ $status['label'] }}
                </span>
            </x-table.td>

            <x-table.td>{{ $backup->size }}</x-table.td>

            <x-table.td>{{ $backup->created_at->format('d/m/Y H:i') }}</x-table.td>

            <x-table.td>{{ $backup->user->name ?? 'Sistema' }}</x-table.td>

            <x-table.td :responsive="false">
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
                                  method="POST"
                                  class="d-inline">
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
            <td colspan="6" class="text-center text-muted py-4">
                Nenhum backup encontrado no histórico.
            </td>
        </tr>
    @endforelse
</x-table.table>
