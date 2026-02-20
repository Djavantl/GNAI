@extends('layouts.master')

@section('title', "Fila de Espera #$waitlist->id")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Filas de Espera' => route('inclusive-radar.waitlists.index'),
        $waitlist->id => null
    ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes da Fila de Espera</h2>
            <p class="text-muted">Visualize informações da solicitação, status e histórico do recurso.</p>
        </div>

        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID no Sistema</span>
            <span class="badge bg-purple fs-6">{{ $waitlist->id }}</span>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Recurso --}}
            <x-forms.section title="Recurso Solicitado" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Nome do Recurso" column="col-md-6" isBox="true">
                    {{ $waitlist->waitlistable->name ?? ($waitlist->waitlistable->title ?? 'Item não identificado') }}
                </x-show.info-item>

                <x-show.info-item label="Tipo de Recurso" column="col-md-6" isBox="true">
                    {{ $waitlist->waitlistable_type === 'assistive_technology' ? 'Tecnologia Assistiva' : 'Material Educacional' }}
                </x-show.info-item>

                <x-show.info-item label="Quantidade Disponível" column="col-md-6" isBox="true">
                    {{ $waitlist->waitlistable->quantity_available ?? 'N/A' }}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Beneficiário e Usuário --}}
            <x-forms.section title="Beneficiário e Usuário Responsável" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Estudante (Beneficiário)" column="col-md-6" isBox="true">
                    {{ $waitlist->student->person->name ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Profissional (Beneficiário)" column="col-md-6" isBox="true">
                    {{ $waitlist->professional->person->name ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Usuário Autenticado (Responsável)" column="col-md-6" isBox="true">
                    {{ $waitlist->user->name ?? '---' }}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 3: Datas e Status --}}
            <x-forms.section title="Status e Datas" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Data da Solicitação" column="col-md-6" isBox="true">
                    {{ $waitlist->requested_at->format('d/m/Y H:i') }}
                </x-show.info-item>

                @php
                    $currentStatus = $waitlist->status instanceof \App\Enums\InclusiveRadar\WaitlistStatus
                        ? $waitlist->status
                        : \App\Enums\InclusiveRadar\WaitlistStatus::tryFrom($waitlist->status);
                @endphp

                <x-show.info-item label="Status da Solicitação" column="col-md-6" isBox="true">
                    {{ $currentStatus?->label() ?? $waitlist->status }}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 4: Observações --}}
            <x-forms.section title="Observações" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Observações" column="col-md-12" isBox="true">
                    {{ $waitlist->observation ?? '---' }}
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small d-flex align-items-center">
                    <i class="fas fa-id-card me-1"></i> ID do Sistema: #{{ $waitlist->id }}
                </div>

                <div class="d-flex gap-3">
                    <x-buttons.link-button :href="route('inclusive-radar.waitlists.edit', $waitlist)" variant="warning">
                        <i class="fas fa-edit"></i> Editar Solicitação
                    </x-buttons.link-button>

                    @if($currentStatus === \App\Enums\InclusiveRadar\WaitlistStatus::WAITING)
                        <form action="{{ route('inclusive-radar.waitlists.cancel', $waitlist) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja cancelar esta solicitação?')"
                            >
                                <i class="fas fa-times"></i> Cancelar Solicitação
                            </x-buttons.submit-button>
                        </form>
                    @endif

                    <x-buttons.link-button :href="route('inclusive-radar.waitlists.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                    </x-buttons.link-button>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/waitlists.js')
    @endpush
@endsection
