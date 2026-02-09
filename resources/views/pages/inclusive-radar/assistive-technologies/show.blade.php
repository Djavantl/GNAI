@extends('layouts.app')

@section('title', "$assistiveTechnology->name")

@section('content')
    {{-- Cabeçalho --}}
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Tecnologias Assistivas' => route('inclusive-radar.assistive-technologies.index'),
            $assistiveTechnology->name => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes da Tecnologia Assistiva</h2>
            <p class="text-muted">Visualize as informações cadastrais, histórico de vistorias e gestão do equipamento.</p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">Patrimônio Atual</span>
            <span class="badge bg-purple fs-6">{{ $assistiveTechnology->asset_code ?? 'SEM CÓDIGO' }}</span>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Identificação do Recurso --}}
            <x-forms.section title="Identificação do Recurso" />
            <div class="row g-3">
                <x-show.info-item label="Nome da Tecnologia" column="col-md-6" isBox="true">
                    <strong>{{ $assistiveTechnology->name }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Descrição Detalhada" column="col-md-12" isBox="true">
                    {{ $assistiveTechnology->description ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Categoria / Tipo" column="col-md-6" isBox="true">
                    {{ $assistiveTechnology->type->name ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Patrimônio / Tombamento" column="col-md-6" isBox="true">
                    {{ $assistiveTechnology->asset_code ?? '---' }}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Especificações Técnicas (Atributos Dinâmicos) --}}
            @if(count($attributeValues) > 0)
                <x-forms.section title="Especificações Técnicas" />
                <div class="row g-3">
                    @foreach($attributeValues as $attributeId => $value)
                        @php
                            $attributeLabel = $assistiveTechnology->attributeValues
                                ->firstWhere('attribute_id', $attributeId)?->attribute->label ?? '---';
                        @endphp

                        <x-show.info-item :label="$attributeLabel" column="col-md-6" isBox="true">
                            {{ $value ?? '---' }}
                        </x-show.info-item>
                    @endforeach
                </div>
            @endif

            {{-- SEÇÃO 3: Histórico de Vistorias --}}
            <x-forms.section title="Histórico de Vistorias" />
            <div class="col-12 mb-4 px-4">
                <div class="history-timeline p-4 border rounded bg-light" style="max-height: 450px; overflow-y: auto;">
                    @forelse($assistiveTechnology->inspections()->with('images')->latest('inspection_date')->get() as $inspection)
                        <x-forms.inspection-history-card :inspection="$inspection" />
                    @empty
                        <div class="text-center py-5 text-muted bg-white rounded border border-dashed">
                            <i class="fas fa-history fa-3x mb-3 opacity-20"></i>
                            <p class="fw-bold">Nenhum histórico encontrado para este recurso.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SEÇÃO 4: Gestão e Público --}}
            <x-forms.section title="Gestão e Público" />

            {{-- Linha 1: Quantidade Total | Status do Recurso --}}
            <div class="row g-3 mb-3">
                <x-show.info-item label="Quantidade Total" column="col-md-6" isBox="true">
                    {{ $assistiveTechnology->quantity }}
                </x-show.info-item>

                <x-show.info-item label="Status do Recurso" column="col-md-6" isBox="true">
                    {{ $assistiveTechnology->resourceStatus->name ?? '---' }}
                </x-show.info-item>
            </div>

            {{-- Linha 2: Requer Treinamento | Ativo no Sistema --}}
            <div class="row g-3 mb-3">
                <x-show.info-item label="Requer Treinamento" column="col-md-6" isBox="true">
                    {{ $assistiveTechnology->requires_training ? 'Sim' : 'Não' }}
                </x-show.info-item>

                <x-show.info-item label="Ativo no Sistema" column="col-md-6" isBox="true">
                    {{ $assistiveTechnology->is_active ? 'Sim' : 'Não' }}
                </x-show.info-item>
            </div>

            {{-- Linha 3: Público-Alvo (linha completa) --}}
            <div class="row g-3">
                <x-show.info-item label="Público-Alvo" column="col-md-12" isBox="true">
                    {{ $assistiveTechnology->deficiencies->pluck('name')->join(', ') ?: '---' }}
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i> ID do Sistema: #{{ $assistiveTechnology->id }}
                </div>

                <div class="d-flex gap-3">
                    <form action="{{ route('inclusive-radar.assistive-technologies.destroy', $assistiveTechnology) }}"
                          method="POST"
                          onsubmit="return confirm('ATENÇÃO: Esta ação excluirá todos os dados do recurso. Confirmar?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            Excluir Recurso
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('inclusive-radar.assistive-technologies.edit', $assistiveTechnology)" variant="warning">
                        Editar Recurso
                    </x-buttons.link-button>

                    <x-buttons.link-button :href="route('inclusive-radar.assistive-technologies.index')" variant="secondary">
                        Voltar para Lista
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection
