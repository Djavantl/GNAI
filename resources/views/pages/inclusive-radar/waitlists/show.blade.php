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
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Detalhes da Fila de Espera</h2>
            <p class="text-muted">Visualize informações da solicitação, status e histórico do recurso.</p>
        </div>

        <div>
            <x-buttons.link-button :href="route('inclusive-radar.waitlists.edit', $waitlist)" variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('inclusive-radar.waitlists.index')" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Recurso Solicitado --}}
            <x-forms.section title="Recurso Solicitado" />

            <div class="col-md-12 mb-4 px-4">
                <div class="p-3 border rounded bg-light d-flex align-items-center gap-3">
                    <div class="bg-purple-dark text-white p-3 rounded shadow-sm" style="background-color: #4c1d95;">
                        <i class="fas {{ $waitlist->waitlistable_type === 'assistive_technology' ? 'fa-microchip' : 'fa-book' }} fa-lg"></i>
                    </div>

                    <div>
                        <h5 class="mb-0 fw-bold">
                            @php
                                $type = $waitlist->waitlistable_type;
                                $id = $waitlist->waitlistable_id;

                                // Lógica de rota usando os aliases do MorphMap
                                $resourceRoute = match($type) {
                                    'assistive_technology'            => route('inclusive-radar.assistive-technologies.show', $id),
                                    'accessible_educational_material' => route('inclusive-radar.accessible-educational-materials.show', $id),
                                    default                           => '#',
                                };
                            @endphp

                            <a href="{{ $resourceRoute }}"
                               class="text-purple-dark text-decoration-none hover-underline"
                               target="_blank"
                               aria-label="Ver detalhes do recurso: {{ $waitlist->waitlistable->name }} (abre em nova aba)">
                                {{ $waitlist->waitlistable->name ?? ($waitlist->waitlistable->title ?? 'Recurso') }}
                                <i class="fas fa-external-link-alt ms-1" aria-hidden="true" style="font-size: 0.70rem;"></i>
                            </a>
                        </h5>
                        <small class="text-muted text-uppercase">Patrimônio: {{ $waitlist->waitlistable->asset_code ?? 'N/A' }}</small>
                    </div>
                </div>
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

                    $statusColor = $currentStatus?->color() ?? 'secondary';
                @endphp

                {{-- Status da solicitação seguindo o padrão minimalista de TA: Negrito + Uppercase --}}
                <x-show.info-item label="Status da Solicitação" column="col-md-6" isBox="true">
                    <span class="text-{{ $statusColor }} fw-bold text-uppercase">
                        {{ $currentStatus?->label() ?? $waitlist->status }}
                    </span>
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
                    <i class="fas fa-id-card me-1" aria-hidden="true"></i> ID no Sistema: #{{ $waitlist->id }}
                    <x-buttons.pdf-button :href="route('inclusive-radar.waitlists.pdf', $waitlist)" class="ms-1" />
                </div>

                <div class="d-flex gap-3">
                    @if($currentStatus === \App\Enums\InclusiveRadar\WaitlistStatus::WAITING)
                        <form action="{{ route('inclusive-radar.waitlists.cancel', $waitlist) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja cancelar esta solicitação?')"
                            >
                                <i class="fas fa-times"></i> Cancelar
                            </x-buttons.submit-button>
                        </form>
                    @endif

                    <form action="{{ route('inclusive-radar.waitlists.destroy', $waitlist) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja excluir esta solicitação?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('inclusive-radar.waitlists.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/waitlists.js')
    @endpush
@endsection
