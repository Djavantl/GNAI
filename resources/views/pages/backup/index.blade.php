@extends('layouts.master')

@section('title', 'Gerenciamento de Backups')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Backups' => route('backup.backups.index'),
        ]" />
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Gerenciamento de Backups"
            subtitle="Visualize e administre as cópias de segurança do sistema."
        >
            <div class="d-flex gap-2">
                @can('backup.upload')
                    <form action="{{ route('backup.backups.upload') }}" method="POST" enctype="multipart/form-data" id="form-upload-backup">
                        @csrf
                        <input type="file"
                               name="backup_file"
                               id="input-backup-file"
                               class="d-none"
                               accept=".zip"
                               onchange="if(this.value) { this.form.submit(); }">

                        <x-buttons.link-button
                            type="button"
                            variant="outline-primary"
                            onclick="document.getElementById('input-backup-file').click()"
                        >
                            <i class="fas fa-upload"></i> Importar Backup
                        </x-buttons.link-button>
                    </form>
                @endcan

                @can('backup.store')
                    <form action="{{ route('backup.backups.store') }}" method="POST">
                        @csrf
                        <x-buttons.submit-button type="submit" variant="new">
                            <i class="fas fa-plus-circle"></i> Gerar Novo
                        </x-buttons.submit-button>
                    </form>
                @endcan
            </div>
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#backups-table"
                action="{{ route('backup.backups.index') }}"
                :fields="[
                    ['name' => 'name', 'placeholder' => 'Filtrar por nome...', 'column' => 'col-md-5'],
                    ['name' => 'status', 'type' => 'select', 'options' => [
                        '' => 'Todos os Status',
                        'success' => 'Sucesso',
                        'failed' => 'Falha',
                        'archived' => 'Arquivado'
                    ], 'column' => 'col-md-3'],
                    ['name' => 'user_id', 'type' => 'select', 'options' => $users->mapWithKeys(fn($u) => [$u->id => $u->name])->prepend('Todos os Responsáveis', ''), 'column' => 'col-md-4']
                ]"
            />
        </div>

        <div id="backups-table" class="p-3">
            @include('pages.backup.partials.table')
        </div>
    </div>

    @canany(['backup.restore', 'backup.destroy'])
        <x-modal.confirm-action
            id="backupActionModal"
            title="Confirmar ação"
            message="Revise os detalhes desta ação antes de continuar."
            confirmText="Confirmar"
            confirmVariant="warning"
            method="POST"
        />

        <template id="restoreBackupPasswordTemplate">
            <div class="pt-2">
                <label for="restore-backup-password" class="form-label fw-semibold">
                    Confirme sua senha
                </label>
                <input
                    type="password"
                    name="password"
                    id="restore-backup-password"
                    class="form-control"
                    autocomplete="current-password"
                    required
                >
                <small class="text-muted d-block mt-2">
                    Esta confirmação é obrigatória porque a restauração sobrescreve dados do sistema.
                </small>
            </div>
        </template>
    @endcanany

    <div class="mt-4 alert alert-info d-flex align-items-center border-0 shadow-sm" role="alert">
        <i class="fas fa-shield-alt me-3 fa-lg text-primary"></i>
        <div>
            <span class="fw-bold d-block">Política de Armazenamento</span>
            <small>
                Os backups são armazenados em <code class="fw-bold text-dark">storage/app/private/{{ config('backup.backup.name') }}</code>.
                O sistema mantém sempre os 30 registros mais recentes; backups excedentes são removidos automaticamente com seus arquivos físicos.
            </small>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
