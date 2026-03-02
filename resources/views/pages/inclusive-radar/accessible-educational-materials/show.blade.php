@extends('layouts.master')

@section('title', $material->name)

@section('content')
    {{-- Breadcrumb --}}
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Materiais Pedagógicos Acessíveis' => route('inclusive-radar.accessible-educational-materials.index'),
            $material->name => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
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

            {{-- SEÇÃO 1: Identificação --}}
            <x-forms.section title="Identificação do Material" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Título do Material" column="col-md-12" isBox="true">
                    <strong>{{ $material->name }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Descrição Detalhada" column="col-md-12" isBox="true">
                    {!! nl2br(e($material->notes)) ?: '---' !!}
                </x-show.info-item>

                <x-show.info-item label="Natureza do Recurso" column="col-md-6" isBox="true">
                    {{ $material->is_digital ? 'Recurso Digital' : 'Recurso Físico' }}
                </x-show.info-item>

                <x-show.info-item label="Patrimônio / Tombamento" column="col-md-6" isBox="true">
                    <strong>{{ $material->asset_code ?? 'SEM CÓDIGO' }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Recursos de Acessibilidade" column="col-md-12" isBox="true">
                    <div class="tag-container">
                        @forelse($material->accessibilityFeatures->sortBy('name') as $feature)
                            <x-show.tag color="light">{{ $feature->name }}</x-show.tag>
                        @empty
                            <span class="text-muted">Nenhum recurso de acessibilidade definido.</span>
                        @endforelse
                    </div>
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: TREINAMENTOS --}}
            <x-forms.section title="Treinamentos e Capacitações" />
            <div class="col-12 mt-4">
                <div class="px-4 mb-4">
                    @if($material->trainings->count() > 0)
                        <div class="p-0 border rounded bg-white shadow-sm overflow-hidden">
                            <x-table.table :headers="['Título', 'Status', 'Ações']" caption="Treinamentos vinculados">
                                @foreach($material->trainings as $training)
                                    <tr>
                                        <x-table.td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-chalkboard-teacher text-purple me-2" aria-hidden="true"></i>
                                                <span class="fw-bold text-dark">{{ $training->title }}</span>
                                            </div>
                                        </x-table.td>
                                        <x-table.td>
                                <span class="text-{{ $training->is_active ? 'success' : 'secondary' }} fw-bold text-uppercase">
                                    {{ $training->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                                        </x-table.td>
                                        <x-table.td>
                                            <x-table.actions>
                                                <x-buttons.link-button :href="route('inclusive-radar.trainings.show', $training)" variant="info">
                                                    <i class="fas fa-eye"></i> Ver
                                                </x-buttons.link-button>
                                            </x-table.actions>
                                        </x-table.td>
                                    </tr>
                                @endforeach
                            </x-table.table>
                        </div>
                    @else
                        <div class="text-center py-5 border rounded bg-light border-dashed" role="status">
                            <p class="text-muted italic mb-0">Nenhum treinamento cadastrado para este material.</p>
                        </div>
                    @endif

                    {{-- Botão Adicionar (Seguindo o padrão TA) --}}
                    <div class="text-end mt-3">
                        <x-buttons.link-button :href="route('inclusive-radar.trainings.create', ['type' => 'accessible_educational_material', 'id' => $material->id])" variant="primary" class="btn-sm">
                            <i class="fas fa-plus me-1"></i> Adicionar Treinamento
                        </x-buttons.link-button>
                    </div>
                </div>
            </div>

            {{-- SEÇÃO 3: Histórico de Vistorias --}}
            <x-forms.section title="Histórico de Vistorias" />
            <div class="col-12 mb-4 px-4 pb-4">
                <div class="history-timeline p-4 border rounded bg-light" style="max-height: 450px; overflow-y:auto;">
                    @forelse($material->inspections()->with('images')->latest('inspection_date')->get() as $inspection)
                        <div class="mb-3" style="cursor:pointer;" onclick="window.location='{{ route('inclusive-radar.accessible-educational-materials.inspection.show', [$material, $inspection]) }}'">
                            <x-forms.inspection-history-card :inspection="$inspection"/>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted bg-white rounded border border-dashed">
                            <p class="fw-bold">Nenhum histórico encontrado.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SEÇÃO 4: Gestão e Público --}}
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
                        @forelse($material->deficiencies->sortBy('name') as $deficiency)
                            <x-show.tag color="light">{{ $deficiency->name }}</x-show.tag>
                        @empty
                            <span class="text-muted">Nenhum público-alvo definido.</span>
                        @endforelse
                    </div>
                </x-show.info-item>
            </div>

            {{-- Rodapé / Ações --}}
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
                            Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('inclusive-radar.accessible-educational-materials.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </footer>
        </main>
    </div>
@endsection
