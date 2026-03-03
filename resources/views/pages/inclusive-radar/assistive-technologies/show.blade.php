@extends('layouts.master')

@section('title', $assistiveTechnology->name)

@section('content')
    {{-- Cabeçalho --}}
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Tecnologias Assistivas' => route('inclusive-radar.assistive-technologies.index'),
            $assistiveTechnology->name => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Detalhes da Tecnologia Assistiva</h2>
            <p class="text-muted mb-0">
                Visualize as informações cadastrais, histórico de vistorias e gestão do equipamento.
            </p>
        </header>

        <div role="group">
            <x-buttons.link-button
                :href="route('inclusive-radar.assistive-technologies.edit', $assistiveTechnology)"
                variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>

            <x-buttons.link-button
                :href="route('inclusive-radar.assistive-technologies.index')"
                variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <main class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Identificação --}}
            <x-forms.section title="Identificação do Recurso" />

            <div class="row g-3 px-4 pb-4">

                <x-show.info-item label="Nome da Tecnologia" column="col-md-4" isBox="true">
                    <strong>{{ $assistiveTechnology->name }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Natureza do Recurso" column="col-md-4" isBox="true">
                    {{ $assistiveTechnology->is_digital ? 'Recurso Digital' : 'Recurso Físico' }}
                </x-show.info-item>

                <x-show.info-item label="Patrimônio / Tombamento" column="col-md-4" isBox="true">
                    <strong>{{ $assistiveTechnology->asset_code ?? 'SEM CÓDIGO' }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Descrição Detalhada" column="col-md-12" isBox="true">
                    {!! nl2br(e($assistiveTechnology->notes)) ?: '---' !!}
                </x-show.info-item>
            </div>


            {{-- SEÇÃO 2: TREINAMENTOS --}}
            <x-forms.section title="Treinamentos e Capacitações" />
            <div class="col-12 mt-4">
                <div class="px-4 mb-4">
                    @if($assistiveTechnology->trainings->count() > 0)
                        <div class="p-0 border rounded bg-white shadow-sm overflow-hidden">
                            <x-table.table :headers="['Título', 'Status', 'Ações']" caption="Treinamentos vinculados">
                                @foreach($assistiveTechnology->trainings as $training)
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
                            <p class="text-muted italic mb-0">Nenhum treinamento cadastrado para este recurso.</p>
                        </div>
                    @endif
                    <div class="text-end mt-3">
                        <x-buttons.link-button :href="route('inclusive-radar.trainings.create', ['type' => 'assistive_technology', 'id' => $assistiveTechnology->id])" variant="primary" class="btn-sm">
                            <i class="fas fa-plus me-1"></i> Adicionar Treinamento
                        </x-buttons.link-button>
                    </div>
                </div>
            </div>


            {{-- SEÇÃO 3: Histórico de Vistorias --}}
            <x-forms.section title="Histórico de Vistorias" />

            <div class="col-12 mb-4 px-4 pb-4">
                <div class="history-timeline p-4 border rounded bg-light"
                     style="max-height: 450px; overflow-y: auto;">

                    @forelse($assistiveTechnology->inspections->sortByDesc('inspection_date') as $inspection)
                        <div class="mb-3"
                             onclick="window.location='{{ route('inclusive-radar.assistive-technologies.inspection.show', [$assistiveTechnology, $inspection]) }}'"
                             style="cursor:pointer;">
                            <x-forms.inspection-history-card :inspection="$inspection" />
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted bg-white rounded border border-dashed">
                            <p class="fw-bold">Nenhuma vistoria registrada.</p>
                        </div>
                    @endforelse

                </div>
            </div>


            {{-- SEÇÃO 4: Gestão --}}
            <x-forms.section title="Gestão e Público" />

            <div class="row g-3 px-4 pb-4">

                <x-show.info-item label="Quantidade Total" column="col-md-6" isBox="true"
                                  :value="$assistiveTechnology->quantity" />

                <x-show.info-item label="Quantidade Disponível" column="col-md-6" isBox="true"
                                  :value="$assistiveTechnology->quantity_available ?? '---'" />

                <x-show.info-item label="Status do Recurso" column="col-md-4" isBox="true">
                    <span class="fw-bold text-{{ $assistiveTechnology->status?->color() ?? 'secondary' }} text-uppercase">
                        {{ $assistiveTechnology->status?->label() ?? '---' }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Permite Empréstimos" column="col-md-4" isBox="true">
                    <span class="text-{{ $assistiveTechnology->is_loanable ? 'success' : 'secondary' }} fw-bold text-uppercase">
                        {{ $assistiveTechnology->is_loanable ? 'Sim' : 'Não' }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Status no Sistema" column="col-md-4" isBox="true">
                    <span class="text-{{ $assistiveTechnology->is_active ? 'success' : 'secondary' }} fw-bold text-uppercase">
                        {{ $assistiveTechnology->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Público-alvo (Deficiências Atendidas)" column="col-md-12" isBox="true">
                    <div class="tag-container">
                        @forelse($assistiveTechnology->deficiencies->sortBy('name') as $deficiency)
                            <x-show.tag color="light">{{ $deficiency->name }}</x-show.tag>
                        @empty
                            <span class="text-muted">Nenhum público-alvo definido.</span>
                        @endforelse
                    </div>
                </x-show.info-item>

            </div>


            {{-- Rodapé --}}
            <footer class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">

                <div class="text-muted small">
                    ID no Sistema: #{{ $assistiveTechnology->id }}
                    <x-buttons.pdf-button :href="route('inclusive-radar.assistive-technologies.pdf', $assistiveTechnology)" class="ms-1" />
                </div>

                <div class="d-flex gap-2">
                    <x-buttons.link-button
                        :href="route('inclusive-radar.assistive-technologies.logs', $assistiveTechnology)"
                        variant="secondary-outline">
                        Logs
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.assistive-technologies.destroy', $assistiveTechnology) }}"
                          method="POST"
                          onsubmit="return confirm('Deseja excluir permanentemente?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button
                        :href="route('inclusive-radar.assistive-technologies.index')"
                        variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </footer>
        </main>
    </div>
@endsection
