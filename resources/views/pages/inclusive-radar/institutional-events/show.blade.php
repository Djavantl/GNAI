@extends('layouts.master')

@section('title', $event->title)

@section('content')
    <div class="mb-5">
        <nav aria-label="Breadcrumb">
            <x-breadcrumb :items="[
                'Home' => route('dashboard'),
                'Agenda Institucional' => route('inclusive-radar.institutional-events.index'),
                $event->title => null
            ]" />
        </nav>
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h1 class="text-title h2">Detalhes da Agenda Institucional</h1>
            <p class="text-muted mb-0">Visualize as informações do registro da agenda institucional.</p>
        </header>
        <div role="group" aria-label="Ações principais">
            <x-buttons.link-button
                :href="route('inclusive-radar.institutional-events.edit', $event)"
                variant="warning"
                label="Editar informações deste registro"
            >
                <i class="fas fa-edit" aria-hidden="true"></i> Editar
            </x-buttons.link-button>

            <x-buttons.link-button
                href="{{ route('inclusive-radar.institutional-events.index') }}"
                variant="secondary"
                label="Voltar para a lista da agenda institucional"
            >
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <main class="custom-table-card bg-white shadow-sm">

            {{-- Informações Principais --}}
            <x-forms.section title="Informações do Registro" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Título" column="col-md-12" isBox="true">
                    <strong>{{ $event->title }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Descrição" column="col-md-12" isBox="true">
                    <div class="text-dark">
                        {!! nl2br(e($event->description)) ?: '<span class="text-muted">Nenhuma descrição fornecida.</span>' !!}
                    </div>
                </x-show.info-item>

                <x-show.info-item label="Data de Início" column="col-md-6" isBox="true">
                    {{ $event->start_date?->format('d/m/Y') }}
                </x-show.info-item>

                <x-show.info-item label="Horário de Início" column="col-md-6" isBox="true">
                    {{ $event->start_time?->format('H:i') }}
                </x-show.info-item>

                <x-show.info-item label="Data de Término" column="col-md-6" isBox="true">
                    {{ $event->end_date?->format('d/m/Y') }}
                </x-show.info-item>

                <x-show.info-item label="Horário de Término" column="col-md-6" isBox="true">
                    {{ $event->end_time?->format('H:i') }}
                </x-show.info-item>
            </div>

            {{-- Detalhes Adicionais --}}
            <x-forms.section title="Detalhes Adicionais" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Local" column="col-md-6" isBox="true">
                    {{ $event->location ?: '-' }}
                </x-show.info-item>

                <x-show.info-item label="Organizador" column="col-md-6" isBox="true">
                    {{ $event->organizer ?: '-' }}
                </x-show.info-item>

                <x-show.info-item label="Público Alvo" column="col-md-12" isBox="true">
                    {{ $event->audience ?: '-' }}
                </x-show.info-item>
            </div>

            {{-- Configurações --}}
            <x-forms.section title="Configurações de Visibilidade" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Status" column="col-md-12" isBox="true">
                    <span class="text-{{ $event->is_active ? 'success' : 'secondary' }} fw-bold text-uppercase" role="status">
                        {{ $event->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <footer class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1" aria-hidden="true"></i> ID no Sistema: #{{ $event->id }}
                </div>

                <div class="d-flex gap-2" role="group" aria-label="Ações de gestão do registro">
                    <form action="{{ route('inclusive-radar.institutional-events.destroy', $event) }}" method="POST" onsubmit="return confirm('Deseja excluir permanentemente este registro?')">
                        @csrf @method('DELETE')
                        <x-buttons.submit-button variant="danger" label="Excluir este registro">
                            <i class="fas fa-trash-alt" aria-hidden="true"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button
                        href="{{ route('inclusive-radar.institutional-events.index') }}"
                        variant="secondary"
                        label="Voltar para a lista"
                    >
                        <i class="fas fa-arrow-left" aria-hidden="true"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </footer>
        </main>
    </div>
@endsection
