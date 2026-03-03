@extends('layouts.master')

@section('title', "Backup - $backup->file_name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Backups' => route('backup.backups.index'),
            $backup->file_name => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Detalhes do Registro de Backup</h2>
            <p class="text-muted">Informações técnicas e metadados da cópia de segurança.</p>
        </div>

        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('backup.backups.edit', $backup->id)" variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('backup.backups.index')" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm rounded overflow-hidden">

            {{-- SEÇÃO 1: Detalhes do Arquivo (Mesma posição do Edit) --}}
            <x-forms.section title="Identificação do Arquivo Físico" />

            <div class="col-md-12 mb-4 px-4">
                <div class="p-3 border rounded bg-light d-flex align-items-center gap-3">
                    <div class="bg-primary text-white p-3 rounded shadow-sm" style="background-color: #2563eb;">
                        <i class="fas fa-file-archive fa-lg"></i>
                    </div>

                    <div class="overflow-hidden">
                        <h5 class="mb-0 fw-bold text-dark text-truncate">
                            {{ $backup->file_name }}
                        </h5>
                        <small class="text-muted text-uppercase d-block mt-1">
                            Caminho no Servidor: <code class="text-primary fw-bold">{{ $backup->file_path }}</code>
                        </small>
                    </div>
                </div>
            </div>

            {{-- SEÇÃO 2: Metadados --}}
            <x-forms.section title="Metadados para Exibição" />

            <div class="px-4">
                <div class="row g-3 mb-4">
                    <x-show.info-item label="Nome de Exibição (Alias)" column="col-md-12" isBox="true">
                        {{ $backup->file_name }}
                    </x-show.info-item>

                    <x-show.info-item label="Tamanho do Arquivo" column="col-md-4" isBox="true">
                        {{ $backup->size }}
                    </x-show.info-item>

                    <x-show.info-item label="Data de Geração" column="col-md-4" isBox="true">
                        {{ $backup->created_at->format('d/m/Y H:i') }}
                    </x-show.info-item>

                    <x-show.info-item label="Responsável" column="col-md-4" isBox="true">
                        {{ $backup->user->name ?? 'Sistema' }}
                    </x-show.info-item>
                </div>
            </div>

            {{-- SEÇÃO 3: Status e Governança --}}
            <x-forms.section title="Status e Governança" />

            <div class="px-4">
                <div class="row g-3 mb-4">
                    <x-show.info-item label="Status do Backup" column="col-md-6" isBox="true">
                        @php
                            $statusColors = [
                                'success'  => 'success',
                                'failed'   => 'danger',
                                'archived' => 'info'
                            ];
                            $statusLabels = [
                                'success'  => 'Sucesso (Arquivo íntegro)',
                                'failed'   => 'Falha (Problema no dump)',
                                'archived' => 'Arquivado (Protegido)'
                            ];
                            $color = $statusColors[$backup->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}-subtle text-{{ $color }}-emphasis border px-3 py-2">
                            <i class="fas fa-circle me-1 small"></i> {{ $statusLabels[$backup->status] ?? $backup->status }}
                        </span>
                    </x-show.info-item>

                    <div class="col-md-6 d-flex align-items-center">
                        <div class="alert alert-warning border-0 shadow-sm mb-0 w-100 py-3">
                            <small>
                                <i class="fas fa-shield-alt me-2"></i>
                                <strong>Regra de Retenção:</strong>
                                @if($backup->status === 'archived')
                                    Este arquivo está **protegido** contra rotinas de limpeza automática.
                                @else
                                    Este arquivo pode ser removido automaticamente conforme as políticas de limpeza do servidor.
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-database me-1"></i> ID do Registro: #{{ $backup->id }}
                </div>

                <div class="d-flex gap-3">
                    <x-buttons.link-button :href="route('backup.backups.download', $backup->id)" variant="success">
                        <i class="fas fa-download"></i> Baixar Backup
                    </x-buttons.link-button>

                    <form action="{{ route('backup.backups.destroy', $backup->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja excluir este backup permanentemente?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('backup.backups.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </div>

        </div>
    </div>
@endsection
