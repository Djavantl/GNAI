@extends('layouts.master')

@section('title', 'Gerenciamento de Backups')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Backups' => route('backup.backups.index'),
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">

        {{-- HEADER --}}
        <x-table.page-header
            title="Gerenciamento de Backups"
            subtitle="Visualize e administre as cópias de segurança do sistema."
        >
            {{-- Botão de ação --}}
            <form action="{{ route('backup.backups.store') }}" method="POST" class="d-inline">
                @csrf
                <x-buttons.submit-button variant="new">
                    <i class="fas fa-plus-circle"></i> Gerar Novo Backup
                </x-buttons.submit-button>
            </form>
        </x-table.page-header>

        {{-- FILTROS --}}
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

        {{-- TABELA --}}
        <div id="backups-table" class="p-3">
            @include('pages.backup.partials.table')
        </div>
    </div>

    <div class="mt-4 alert alert-info d-flex align-items-center border-0 shadow-sm" role="alert">
        <i class="fas fa-shield-alt me-3 fa-lg text-primary"></i>
        <div>
            <span class="fw-bold d-block">Política de Armazenamento</span>
            <small>
                Os backups são armazenados em <code class="fw-bold text-dark">storage/app/GNAI</code>.
                Arquivos com status <span class="badge bg-info-subtle text-info-emphasis border px-1">Arquivado</span> não serão removidos por limpezas automáticas.
            </small>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
