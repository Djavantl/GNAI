@extends('layouts.master')

@section('title', "$material->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Materiais Pedagógicos Acessíveis' => route('inclusive-radar.accessible-educational-materials.index'),
            $material->name => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes do Material Pedagógico Acessível</h2>
            <p class="text-muted">Visualize as informações cadastrais, histórico de vistorias e gestão do material.</p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID do Registro</span>
            <span class="badge bg-purple fs-6">#{{ $material->id }}</span>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Identificação do Recurso --}}
            <x-forms.section title="Identificação do Recurso" />
            <div class="row g-3">
                <x-show.info-item label="Título do Material" column="col-md-6" isBox="true">
                    <strong>{{ $material->name }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Descrição Detalhada" column="col-md-12" isBox="true">
                    {{ $material->notes ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Categoria / Tipo" column="col-md-6" isBox="true">
                    {{ $material->type->name ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Patrimônio / Tombamento" column="col-md-6" isBox="true">
                    {{ $material->asset_code ?? '---' }}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Especificações Técnicas --}}
            @if(count($attributeValues) > 0)
                <x-forms.section title="Especificações Técnicas" />
                <div class="row g-3">
                    @foreach($attributeValues as $attributeId => $value)
                        @php
                            $attributeLabel = $material->attributeValues
                                ->firstWhere('attribute_id', $attributeId)?->attribute->label ?? '---';
                        @endphp

                        <x-show.info-item :label="$attributeLabel" column="col-md-6" isBox="true">
                            {{ $value ?? '---' }}
                        </x-show.info-item>
                    @endforeach
                </div>
            @endif

            {{-- SEÇÃO 3: Recursos de Acessibilidade --}}
            <x-forms.section title="Recursos de Acessibilidade" />
            <x-show.info-item label="Recursos presentes no material" column="col-md-12" isBox="true">
                @if($material->accessibilityFeatures->isNotEmpty())
                    <div class="tag-container">
                        @foreach($material->accessibilityFeatures->sortBy('name') as $feature)
                            <x-show.tag color="light">{{ $feature->name }}</x-show.tag>
                        @endforeach
                    </div>
                @else
                    Nenhum recurso informado
                @endif
            </x-show.info-item>

            {{-- SEÇÃO 4: Histórico de Vistorias --}}
            <x-forms.section title="Histórico de Vistorias" />
            <div class="col-12 mb-4 px-4">
                <div class="history-timeline p-4 border rounded bg-light" style="max-height: 450px; overflow-y: auto;">
                    @forelse($material->inspections()->with('images')->latest('inspection_date')->get() as $inspection)
                        <x-forms.inspection-history-card :inspection="$inspection" />
                    @empty
                        <div class="text-center py-5 text-muted bg-white rounded border border-dashed">
                            <i class="fas fa-history fa-3x mb-3 opacity-20"></i>
                            <p class="fw-bold">Nenhum histórico registrado para este material.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SEÇÃO 5: Gestão e Público --}}
            <x-forms.section title="Gestão e Público" />

            {{-- Linha 1: Quantidade Total | Status do Material --}}
            <div class="row g-3 mb-3">
                <x-show.info-item label="Quantidade Total" column="col-md-6" isBox="true">
                    {{ $material->quantity }}
                </x-show.info-item>

                <x-show.info-item label="Status do Recurso" column="col-md-6" isBox="true">
                    {{ $material->resourceStatus->name ?? '---' }}
                </x-show.info-item>
            </div>

            {{-- Linha 2: Requer Treinamento | Ativo no Sistema --}}
            <div class="row g-3 mb-3">
                <x-show.info-item label="Requer Treinamento" column="col-md-6" isBox="true">
                    {{ $material->requires_training ? 'Sim' : 'Não' }}
                </x-show.info-item>

                <x-show.info-item label="Ativo no Sistema" column="col-md-6" isBox="true">
                    {{ $material->is_active ? 'Sim' : 'Não' }}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 5: Público-Alvo / Deficiências --}}
            <div class="row g-3">
                <x-show.info-item label="Público-Alvo (Deficiências)" column="col-md-12" isBox="true">
                    @if($material->deficiencies->isNotEmpty())
                        <div class="tag-container">
                            @foreach($material->deficiencies->sortBy('name') as $deficiency)
                                <x-show.tag color="light">{{ $deficiency->name }}</x-show.tag>
                            @endforeach
                        </div>
                    @endif
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small d-flex align-items-center">
                    <i class="fas fa-id-card me-1"></i> ID do Sistema: #{{ $material->id }}
                    <x-buttons.pdf-button
                        :href="route('inclusive-radar.accessible-educational-materials.pdf', $material)"
                        class="ms-3"
                    />
                </div>

                <div class="d-flex gap-3">
                    {{-- Gerar PDF --}}
                    <x-buttons.link-button
                        :href="route('inclusive-radar.accessible-educational-materials.pdf', $material)"
                        target="_blank"
                        variant="primary"
                    >
                        <i class="fas fa-file-pdf"></i> Gerar PDF
                    </x-buttons.link-button>

                    {{-- Excluir Recurso --}}
                    <form action="{{ route('inclusive-radar.accessible-educational-materials.destroy', $material) }}"
                          method="POST"
                          onsubmit="return confirm('ATENÇÃO: Esta ação excluirá todos os dados do material. Confirmar?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir Recurso
                        </x-buttons.submit-button>
                    </form>

                    {{-- Editar Recurso --}}
                    <x-buttons.link-button
                        :href="route('inclusive-radar.accessible-educational-materials.edit', $material)"
                        variant="warning"
                    >
                        <i class="fas fa-edit"></i> Editar Recurso
                    </x-buttons.link-button>

                    {{-- Voltar para Lista --}}
                    <x-buttons.link-button
                        :href="route('inclusive-radar.accessible-educational-materials.index')"
                        variant="secondary"
                    >
                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/accessible-educational-materials.js')
    @endpush
@endsection
