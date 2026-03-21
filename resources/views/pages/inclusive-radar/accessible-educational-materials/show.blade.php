@extends('layouts.master')

@section('title', $material->name)

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Materiais Pedagógicos Acessíveis' => route('inclusive-radar.accessible-educational-materials.index'),
            $material->name => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Detalhes do Material Pedagógico Acessível</h2>
            <p class="text-muted mb-0">
                Visualize informações cadastrais, histórico de vistorias, treinamentos e gestão do material.
            </p>
        </header>

        <div role="group" aria-label="Ações principais">
            <x-buttons.link-button
                :href="route('inclusive-radar.accessible-educational-materials.edit', $material)"
                variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>

            <x-buttons.link-button
                :href="route('inclusive-radar.accessible-educational-materials.index')"
                variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <main class="custom-table-card bg-white shadow-sm">

            <x-forms.section title="Identificação do Material" />

            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Título do Material" column="col-md-12" isBox="true">
                    {{ $material->name }}
                </x-show.info-item>

                <x-show.info-item label="Descrição Detalhada" column="col-md-12" isBox="true">
                    {!! nl2br(e($material->notes)) ?: '---' !!}
                </x-show.info-item>

                <x-show.info-item label="Natureza do Recurso" column="col-md-6" isBox="true">
                    {{ $material->is_digital ? 'Recurso Digital' : 'Recurso Físico' }}
                </x-show.info-item>

                <x-show.info-item label="Patrimônio / Tombamento" column="col-md-6" isBox="true">
                    {{ $material->asset_code ?? 'SEM CÓDIGO' }}
                </x-show.info-item>

                <x-show.info-item label="Recursos de Acessibilidade" column="col-md-12" isBox="true">
                    <div class="tag-container">
                        @forelse($features as $feature)
                            <x-show.tag color="light">{{ $feature->name }}</x-show.tag>
                        @empty
                            <span class="text-muted">Nenhum recurso definido.</span>
                        @endforelse
                    </div>
                </x-show.info-item>
            </div>

            <x-forms.section title="Histórico de Vistorias" />

            <div class="history-timeline p-4 border border-secondary-subtle rounded bg-white" style="max-height: 450px; overflow-y:auto;">
                @forelse($material->inspections as $inspection)
                    <div class="mb-3 cursor-pointer p-2 rounded border shadow-sm hover-shadow"
                         role="button"
                         tabindex="0"
                         data-url="{{ route('inclusive-radar.accessible-educational-materials.inspection.show', [$material, $inspection]) }}"
                         aria-label="Ver detalhes da vistoria de {{ $inspection->inspection_date->format('d/m/Y') }}">
                        <x-forms.inspection-history-card :inspection="$inspection"/>
                    </div>
                @empty
                    <div class="text-center py-5 bg-light rounded border border-dashed">
                        <i class="fas fa-clipboard-list fa-2x text-secondary mb-2"></i>
                        <p class="fw-bold text-dark mb-0">Nenhum histórico de vistoria encontrado.</p>
                    </div>
                @endforelse
            </div>

            <x-forms.section title="Gestão e Público" />

            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Quantidade Total" column="col-md-6" isBox="true" :value="$material->quantity"/>
                <x-show.info-item label="Quantidade Disponível" column="col-md-6" isBox="true" :value="$material->quantity_available ?? '---'"/>

                <x-show.info-item label="Status do Recurso" column="col-md-4" isBox="true">
                    <span class="fw-bold text-{{ $material->status?->color() ?? 'secondary' }} text-uppercase">
                        {{ $material->status?->label() ?? '---' }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Permite Empréstimos" column="col-md-4" isBox="true">
                    <span class="text-{{ $material->is_loanable ? 'success' : 'secondary' }} fw-bold text-uppercase">
                        {{ $material->is_loanable ? 'Sim' : 'Não' }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Status no Sistema" column="col-md-4" isBox="true">
                    <span class="text-{{ $material->is_active ? 'success' : 'secondary' }} fw-bold text-uppercase">
                        {{ $material->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Público-alvo (Deficiências Atendidas)" column="col-md-12" isBox="true">
                    <div class="tag-container">
                        @forelse($deficiencies as $def)
                            <x-show.tag color="light">{{ $def->name }}</x-show.tag>
                        @empty
                            <span class="text-muted">Nenhum público-alvo definido.</span>
                        @endforelse
                    </div>
                </x-show.info-item>
            </div>

            <footer class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small">
                    ID no Sistema: #{{ $material->id }}
                    <x-buttons.pdf-button :href="route('inclusive-radar.accessible-educational-materials.pdf', $material)" class="ms-1" />
                </div>
                <div class="d-flex gap-2">
                    <x-buttons.link-button :href="route('inclusive-radar.accessible-educational-materials.logs', $material)" variant="secondary-outline">
                        <i class="fas fa-history"></i> Logs
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.accessible-educational-materials.destroy', $material) }}" method="POST" onsubmit="return confirm('Deseja excluir permanentemente?')">
                        @csrf @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('inclusive-radar.accessible-educational-materials.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </footer>
        </main>
    </div>
    @vite('resources/js/pages/inclusive-radar/accessible-educational-materials.js')
@endsection
