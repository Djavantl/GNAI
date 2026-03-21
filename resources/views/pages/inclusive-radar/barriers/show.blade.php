@extends('layouts.master')

@section('title', $barrier->name)

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Barreiras' => route('inclusive-radar.barriers.index'),
            $barrier->name => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Detalhes da Barreira</h2>
            <p class="text-muted">
                Visualize as informações cadastradas e o histórico de vistorias:
                <strong>{{ $barrier->name }}</strong>
            </p>
        </div>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('inclusive-radar.barriers.edit', $barrier)" variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('inclusive-radar.barriers.index')" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-show.display-card>
            <div class="row g-0">
                <div class="col-lg-5 border-end">

                    <x-forms.section title="Detalhes da Ocorrência" class="mx-n4" />

                    <div class="px-4">
                        <div class="row g-3">
                            <x-show.info-item label="Título do Relato" column="col-md-8" isBox="true">
                                {{ $barrier->name }}
                            </x-show.info-item>

                            <x-show.info-item label="Data" column="col-md-4" isBox="true">
                                {{ $barrier->identified_at?->format('d/m/Y') ?? 'Não informada' }}
                            </x-show.info-item>

                            <x-show.info-item label="Prioridade" column="col-md-6" isBox="true">
                                @php $prioColor = $barrier->priority?->color() ?? 'secondary'; @endphp
                                <span class="text-{{ $prioColor }} fw-bold text-uppercase">
                                    {{ $barrier->priority?->label() ?? 'Não definida' }}
                                </span>
                            </x-show.info-item>

                            <x-show.info-item label="Categoria" column="col-md-6" isBox="true">
                                {{ $barrier->category?->name ?? 'Não categorizada' }}
                            </x-show.info-item>

                            <x-show.info-item label="Campus / Unidade" column="col-6" isBox="true">
                                {{ $barrier->institution?->name ?? 'Não informado' }}
                            </x-show.info-item>

                            <x-show.info-item label="Local / Ponto de Referência" column="col-6" isBox="true">
                                {{ $barrier->location?->name ?? 'Não informado' }}
                            </x-show.info-item>

                            @if($barrier->location_specific_details)
                                <x-show.info-item label="Complemento" column="col-12" isBox="true">
                                    {{ $barrier->location_specific_details }}
                                </x-show.info-item>
                            @endif

                            <x-show.info-item label="Descrição Detalhada" column="col-12" isBox="true">
                                {{ $barrier->description ?: 'Sem descrição detalhada.' }}
                            </x-show.info-item>
                        </div>
                    </div>

                    <div class="px-4">
                        <div class="row g-3 mb-4">
                            <x-show.info-item label="Pessoa(s) Impactada(s)" column="col-12" isBox="true">
                                @if($barrier->is_anonymous)
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-user-secret text-muted"></i>
                                        <span class="fw-bold text-secondary">Relato Anônimo</span>
                                    </div>
                                @elseif($barrier->not_applicable)
                                    <div>
                                        <span class="fw-bold text-purple-dark">Relato Geral</span>
                                        @if($barrier->affected_person_name || $barrier->affected_person_role)
                                            <div class="mt-2 pt-2 border-top">
                                                <small class="text-muted uppercase fw-bold d-block" style="font-size: 0.65rem;">Identificação Manual</small>
                                                <div class="text-purple-light">
                                                    {{ $barrier->affected_person_name }} {{ $barrier->affected_person_role ? "({$barrier->affected_person_role})" : '' }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="d-flex flex-column gap-3">
                                        @if($barrier->affectedStudent)
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-user-graduate text-primary"></i>
                                                <div>
                                                    <div class="fw-bold text-purple-dark">{{ $barrier->affectedStudent->person->name }}</div>
                                                    <small class="text-muted">Estudante</small>
                                                </div>
                                            </div>
                                        @endif

                                        @if($barrier->affectedProfessional)
                                            <div class="d-flex align-items-center gap-2 {{ $barrier->affectedStudent ? 'pt-2 border-top' : '' }}">
                                                <i class="fas fa-user-tie text-success"></i>
                                                <div>
                                                    <div class="fw-bold text-purple-dark">{{ $barrier->affectedProfessional->person->name }}</div>
                                                    <small class="text-muted">Profissional</small>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!$barrier->affectedStudent && !$barrier->affectedProfessional)
                                            <span class="text-muted">Não informado</span>
                                        @endif
                                    </div>
                                @endif
                            </x-show.info-item>

                            <x-show.info-item label="Deficiências Relacionadas" column="col-12" isBox="true">
                                {{ $barrier->deficiencies->pluck('name')->join(', ') ?: '---' }}
                            </x-show.info-item>
                        </div>
                    </div>

                    <div class="px-4 pb-4">
                        <div class="row g-3">
                            <x-show.info-item label="Relator" column="col-6" isBox="true">
                                {{ $barrier->reporter_display_name }}
                            </x-show.info-item>

                            <x-show.info-item label="Status no Sistema" column="col-6" isBox="true">
                                <span class="text-{{ $barrier->is_active ? 'success' : 'secondary' }} fw-bold text-uppercase">
                                    {{ $barrier->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </x-show.info-item>

                            @if(!$barrier->category?->blocks_map)
                                <x-show.info-item label="Coordenadas" column="col-12" isBox="true">
                                    <span class="font-monospace small">
                                        {{ $barrier->latitude ?? '—' }}, {{ $barrier->longitude ?? '—' }}
                                    </span>
                                </x-show.info-item>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 bg-light">

                    <x-forms.section title="Localização no Mapa"/>

                    <div style="position: relative;">
                        <x-show.maps.barrier
                            :barrier="$barrier"
                            :institution="$barrier->institution"
                            height="450px"
                        />

                        @if($barrier->category?->blocks_map)
                            <div id="map-blocked-overlay"
                                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 1000; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #333; border-radius: 0.375rem; cursor: not-allowed;">
                                <span class="bg-white p-3 rounded shadow-sm border text-center">
                                    <i class="fas fa-lock text-danger mb-2 d-block"></i>
                                    Mapa não se aplica a categoria <br>{{ $barrier->category?->name }}.
                                </span>
                            </div>
                        @endif
                    </div>

                    <x-forms.section title="Histórico de Vistorias"/>

                    <div class="px-4 pb-4 mt-3">
                        <div class="history-timeline custom-scrollbar p-3 border border-secondary-subtle rounded bg-white"
                             style="max-height: 450px; overflow-y:auto;">

                            @forelse($barrier->inspections as $inspection)
                                <div class="mb-3 cursor-pointer p-2 rounded border shadow-sm transition-hover"
                                     role="button"
                                     tabindex="0"
                                     data-url="{{ route('inclusive-radar.barriers.inspection.show', [$barrier, $inspection]) }}"
                                     aria-label="Ver detalhes da vistoria de {{ $inspection->inspection_date?->format('d/m/Y') ?? $inspection->created_at->format('d/m/Y') }}">
                                    <x-forms.inspection-history-card :inspection="$inspection" />
                                </div>
                            @empty
                                <div class="text-center py-5 bg-light rounded border border-dashed">
                                    <i class="fas fa-history fa-2x text-secondary mb-2 opacity-50"></i>
                                    <p class="fw-bold text-dark mb-0">Nenhuma vistoria registrada para esta barreira.</p>
                                    <small class="text-muted">As vistorias ajudam a monitorar o status da resolução.</small>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-white no-print">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i> ID: #{{ $barrier->id }}
                    <x-buttons.pdf-button :href="route('inclusive-radar.barriers.pdf', $barrier)" class="ms-1" />
                </div>

                <div class="d-flex gap-2">
                    <form action="{{ route('inclusive-radar.barriers.destroy', $barrier) }}" method="POST"
                          onsubmit="return confirm('Deseja realmente excluir este registro?')">
                        @csrf @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('inclusive-radar.barriers.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </div>
        </x-show.display-card>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            .border-dashed { border-style: dashed !important; border-width: 2px; }
            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endpush
    @vite('resources/js/pages/inclusive-radar/barriers.js')
@endsection
