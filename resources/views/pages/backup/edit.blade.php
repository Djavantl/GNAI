@extends('layouts.master')

@section('title', "Editar Backup - $backup->file_name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Backups' => route('backup.backups.index'),
            $backup->file_name => route('backup.backups.show', $backup->id),
            'Editar Registro' => null
        ]" />
    </div>

    {{-- Resto do código permanece igual --}}

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Editar Registro de Backup</h2>
            <p class="text-muted mb-0">Ajuste os metadados do arquivo. O caminho físico é mantido para integridade do sistema.</p>
        </header>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('backup.backups.show', $backup)" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('backup.backups.update', $backup->id) }}" method="POST">
            @method('PUT')
            @csrf

            {{-- SEÇÃO 1: Detalhes do Arquivo --}}
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
                            Caminho: <code class="text-primary fw-bold">{{ $backup->file_path }}</code>
                        </small>
                    </div>
                </div>
            </div>

            {{-- SEÇÃO 2: Metadados --}}
            <x-forms.section title="Metadados para Exibição" />

            {{-- Nome de Exibição --}}
            <div class="col-md-12 px-4 mb-3">
                <x-forms.input
                    name="file_name"
                    label="Nome de Exibição (Alias)"
                    required
                    :value="old('file_name', $backup->file_name)"
                />
                <p class="text-muted mt-1 small">
                    <i class="fas fa-info-circle me-1 text-warning"></i>
                    Isso altera apenas como o nome aparece na listagem para os usuários.
                </p>
            </div>

            {{-- Grid de Informações Técnicas com Padding Lateral --}}
            <div class="col-md-12 px-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-forms.input
                            name="size_display"
                            label="Tamanho do Arquivo"
                            :value="$backup->size"
                            disabled
                        />
                    </div>

                    <div class="col-md-4 mb-3">
                        <x-forms.input
                            name="created_at_display"
                            label="Data de Geração"
                            :value="$backup->created_at->format('d/m/Y H:i')"
                            disabled
                        />
                    </div>

                    <div class="col-md-4 mb-3">
                        <x-forms.input
                            name="user_display"
                            label="Responsável"
                            :value="$backup->user->name ?? 'Sistema'"
                            disabled
                        />
                    </div>
                </div>
            </div>

            {{-- SEÇÃO 3: Status --}}
            <x-forms.section title="Status e Governança" />

            <div class="col-md-12 px-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-forms.select
                            name="status"
                            label="Status do Backup"
                            required
                            :options="[
                                'success' => 'Sucesso (Arquivo íntegro)',
                                'failed' => 'Falha (Problema no dump)',
                                'archived' => 'Arquivado (Protegido contra limpeza)'
                            ]"
                            :selected="old('status', $backup->status)"
                        />
                    </div>

                    <div class="col-md-6 mb-3 d-flex align-items-center pt-md-4">
                        <div class="alert alert-warning border-0 shadow-sm mb-0 w-100 py-2">
                            <small><i class="fas fa-shield-alt me-1"></i> <strong>Nota:</strong> Status "Arquivado" ignora limpezas automáticas.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTÕES --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button :href="route('backup.backups.show', $backup)" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save me-1"></i> Salvar Alterações
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
@endsection
