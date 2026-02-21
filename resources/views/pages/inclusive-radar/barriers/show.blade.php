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
        <div>
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

                {{-- ========== COLUNA ESQUERDA ========== --}}
                <div class="col-lg-5 border-end">

                    <x-forms.section title="Localização e Contexto" class="mx-n4" />

                    <div class="row g-3"> {{-- Row principal --}}
                        @if($barrier->no_location || (!$barrier->latitude && !$barrier->longitude))
                            <div class="col-12">
                                <x-show.info-item label="Localização Física" column="col-12" isBox="true">
                                    <span class="text-muted">Sem localização física registrada.</span>
                                </x-show.info-item>
                            </div>
                        @else
                            {{-- Campus e Prédio dividindo a linha --}}
                            <div class="col-md-6">
                                <x-show.info-item label="Campus / Unidade" column="col-12" isBox="true">
                                    {{ $barrier->institution?->name ?? 'Não informado' }}
                                </x-show.info-item>
                            </div>

                            <div class="col-md-6">
                                <x-show.info-item label="Local/Ponto de Referência" column="col-12" isBox="true">
                                    {{ $barrier->location?->name ?? 'Não informado' }}
                                </x-show.info-item>
                            </div>

                            {{-- Complemento logo abaixo --}}
                            @if($barrier->location_specific_details)
                                <div class="col-12">
                                    <x-show.info-item label="Complemento" column="col-12" isBox="true">
                                        {{ $barrier->location_specific_details }}
                                    </x-show.info-item>
                                </div>
                            @endif
                        @endif {{-- O erro estava aqui: o endif precisa vir antes de fechar a div row ou logo após as colunas --}}
                    </div> {{-- Fim da Row --}}

                    {{-- 1. Detalhes da Ocorrência --}}
                    <x-forms.section title="2. Detalhes da Ocorrência" />
                    <div class="row g-3 mb-0">

                        <x-show.info-item label="Nome da Barreira" column="col-6" isBox="true">
                            <strong>{{ $barrier->name }}</strong>
                        </x-show.info-item>

                        <x-show.info-item label="Data de Identificação" column="col-6" isBox="true">
                            {{ $barrier->identified_at?->format('d/m/Y') ?? 'Não informada' }}
                            @if($barrier->resolved_at)
                                <br><small class="text-muted">Resolvido em: {{ $barrier->resolved_at->format('d/m/Y') }}</small>
                            @endif
                        </x-show.info-item>

                        <x-show.info-item label="Prioridade" column="col-6" isBox="true">
                            @php $prioColor = $barrier->priority?->color() ?? 'secondary'; @endphp
                            <span class="badge bg-{{ $prioColor }}-subtle text-{{ $prioColor }}-emphasis border px-3">
                                {{ $barrier->priority?->label() ?? 'Não definida' }}
                            </span>
                        </x-show.info-item>

                        <x-show.info-item label="Categoria" column="col-6" isBox="true">
                            {{ $barrier->category?->name ?? 'Não categorizada' }}
                        </x-show.info-item>

                        <x-show.info-item label="Descrição" column="col-12" isBox="true">
                            {{ $barrier->description ?: 'Sem descrição.' }}
                        </x-show.info-item>
                    </div>

                    {{-- PESSOA IMPACTADA - Utilizando o componente padrão do sistema --}}
                    <div class="row g-3 mb-0">
                        <x-show.info-item label="Pessoa Impactada" column="col-12" isBox="true">
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
                                            @if($barrier->affected_person_name)
                                                <div class="text-muted uppercase fw-bold" style="font-size: 0.65rem;">Nome</div>
                                                <div class="text-purple-light">{{ $barrier->affected_person_name }}</div>
                                            @endif

                                            @if($barrier->affected_person_role)
                                                <div class="text-muted uppercase fw-bold" style="font-size: 0.65rem;">Cargo</div>
                                                <div class="text-purple-light">{{ $barrier->affected_person_role }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                @if($barrier->affectedStudent)
                                    <div>
                                        <div class="small text-muted uppercase fw-bold" style="font-size: 0.65rem;">Estudante</div>
                                        <div class="fw-bold text-purple-dark">{{ $barrier->affectedStudent->person?->name ?? 'Nome não disponível' }}</div>
                                    </div>
                                @elseif($barrier->affectedProfessional)
                                    <div>
                                        <div class="small text-muted uppercase fw-bold" style="font-size: 0.65rem;">Profissional</div>
                                        <div class="fw-bold text-purple-dark">{{ $barrier->affectedProfessional->person?->name ?? 'Nome não disponível' }}</div>
                                    </div>
                                @else
                                    <span class="text-muted italic">Não informado</span>
                                @endif
                            @endif
                        </x-show.info-item>
                    </div>

                    <div class="row g-3 mb-0">
                        <div class="row g-3">
                            <x-show.info-item label="Público-Afetado" column="col-12" isBox="true">
                                {{ $barrier->deficiencies->pluck('name')->join(', ') ?: '---' }}
                            </x-show.info-item>
                        </div>
                    </div>
                    <div class="row g-3 mb-0">
                        <x-show.info-item label="Relator" column="col-6" isBox="true">
                            {{ $barrier->reporter_display_name }}
                        </x-show.info-item>

                        <x-show.info-item label="Status no Sistema" column="col-6" isBox="true">
                            <span class="text-{{ $barrier->is_active ? 'success' : 'secondary' }} fw-bold text-uppercase">
                                {{ $barrier->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </x-show.info-item>
                    </div>
                    <x-show.info-item label="Coordenadas" column="col-12" isBox="true">
                            <span class="font-monospace">
                                {{ $barrier->latitude ?? '—' }}, {{ $barrier->longitude ?? '—' }}
                            </span>
                    </x-show.info-item>
                </div>

                {{-- ========== COLUNA DIREITA ========== --}}
                <div class="col-lg-7 bg-light">
                    {{-- MAPA --}}
                    <x-forms.section title="3. Localização no Mapa" id="map-section-title" />
                    <div class="mb-3 px-4" style="top:20px; z-index:1;">
                        <section aria-labelledby="map-section-title">
                            @if($barrier->no_location || (!$barrier->latitude && !$barrier->longitude))
                                <div class="text-center py-5 text-muted bg-white rounded border m-3">
                                    <i class="fas fa-map-marked-alt fa-3x mb-3 opacity-20"></i>
                                    <p class="fw-bold">Localização não informada</p>
                                </div>
                            @else
                                <x-show.maps.barrier
                                    :barrier="$barrier"
                                    :institution="$barrier->institution"
                                    height="450px"
                                    label="Localização da Barreira"
                                />
                            @endif
                        </section>
                    </div>

                    <x-forms.section title="4. Histórico de Vistorias" />
                    <div class="mt-4 px-4">
                        @forelse($barrier->inspections as $inspection)
                            <div
                                class="inspection-link d-block mb-3"
                                style="cursor:pointer;"
                                onclick="window.location='{{ route('inclusive-radar.barriers.inspection.show', [$barrier, $inspection]) }}'"
                            >
                                <x-forms.inspection-history-card :inspection="$inspection" />
                            </div>
                        @empty
                            <div class="text-center py-4 bg-light rounded border border-dashed">
                                <i class="fas fa-history text-muted mb-2"></i>
                                <p class="text-muted small mb-0">Nenhuma vistoria registrada para esta barreira.</p>
                            </div>
                        @endforelse
                    </div>
                </div> {{-- FIM DA COLUNA DIREITA (col-lg-7) --}}
            </div> {{-- FIM DA ROW (row g-0) --}}
            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1" aria-hidden="true"></i> ID no Sistema: #{{ $barrier->id }}
                    <x-buttons.pdf-button :href="route('inclusive-radar.barriers.pdf', $barrier)" class="ms-1" />
                </div>

                <div class="d-flex gap-3">
                    <form action="{{ route('inclusive-radar.barriers.destroy', $barrier) }}"
                          method="POST"
                          onsubmit="return confirm('ATENÇÃO: Esta ação excluirá todos os dados do recurso. Confirmar?')">
                        @csrf
                        @method('DELETE')
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
            .object-fit-cover { object-fit: cover; }
            .opacity-20 { opacity: 0.2; }
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endpush
@endsection
