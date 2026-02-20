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
            <p class="text-muted mb-0">Visualize as informações cadastrais, histórico de vistorias e gestão do equipamento.</p>
        </header>
        <div role="group" aria-label="Ações principais">
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

            {{-- SEÇÃO 1: Identificação do Recurso --}}
            <x-forms.section title="Identificação do Recurso" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Nome da Tecnologia" column="col-md-12" isBox="true">
                    <strong>{{ $assistiveTechnology->name }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Descrição Detalhada" column="col-md-12" isBox="true">
                    {!! nl2br(e($assistiveTechnology->description)) ?? '---' !!}
                </x-show.info-item>

                <x-show.info-item label="Categoria / Tipo" column="col-md-6" isBox="true" :value="$assistiveTechnology->type->name" />

                <x-show.info-item label="Patrimônio / Tombamento" column="col-md-6" isBox="true">
                    <strong>{{ $assistiveTechnology->asset_code ?? 'SEM CÓDIGO' }}</strong>
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Especificações Técnicas --}}
            @if(count($attributeValues) > 0)
                <x-forms.section title="Especificações Técnicas" />
                <div class="row g-3 px-4 pb-4">
                    @foreach($attributeValues as $attributeId => $value)
                        <x-show.info-item
                            :label="$assistiveTechnology->attributeValues->firstWhere('attribute_id', $attributeId)?->attribute->label ?? 'Campo Técnico'"
                            column="col-md-6"
                            isBox="true"
                            :value="$value"
                        />
                    @endforeach
                </div>
            @endif

            {{-- SEÇÃO 3: TREINAMENTOS --}}
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

            {{-- SEÇÃO 4: Histórico --}}
            <x-forms.section title="Histórico de Vistorias" />
            <div class="col-12 mb-4 px-4 pb-4">
                <div class="history-timeline p-4 border rounded bg-light" style="max-height: 450px; overflow-y: auto;" role="log">
                    @forelse($assistiveTechnology->inspections->sortByDesc('inspection_date') as $inspection)
                        <div class="inspection-link d-block mb-3" style="cursor:pointer;" onclick="window.location='{{ route('inclusive-radar.assistive-technologies.inspection.show', [$assistiveTechnology, $inspection]) }}'" role="link" tabindex="0">
                            <x-forms.inspection-history-card :inspection="$inspection" />
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted bg-white rounded border border-dashed">
                            <p class="fw-bold">Nenhum histórico encontrado.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SEÇÃO 5: Gestão --}}
            <x-forms.section title="Gestão e Público" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Quantidade Total" column="col-md-6" isBox="true" :value="$assistiveTechnology->quantity" />
                <x-show.info-item label="Status do Recurso" column="col-md-6" isBox="true">
                    <span class="badge bg-info-subtle text-info-emphasis border px-3">
                        {{ $assistiveTechnology->resourceStatus->name ?? '---' }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Status no Sistema" column="col-md-12" isBox="true">
                    <span class="text-{{ $assistiveTechnology->is_active ? 'success' : 'secondary' }} fw-bold text-uppercase">
                        {{ $assistiveTechnology->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Público-alvo (Deficiências Atendidas) *" column="col-md-12" isBox="true">
                    <div class="tag-container" role="list">
                        @forelse($assistiveTechnology->deficiencies->sortBy('name') as $deficiency)
                            <x-show.tag color="light" role="listitem">{{ $deficiency->name }}</x-show.tag>
                        @empty
                            <span class="text-muted">Nenhum público-alvo definido.</span>
                        @endforelse
                    </div>
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <footer class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1" aria-hidden="true"></i> ID: #{{ $assistiveTechnology->id }}
                    <x-buttons.pdf-button :href="route('inclusive-radar.assistive-technologies.pdf', $assistiveTechnology)" class="ms-1" />
                    <x-buttons.excel-button :href="route('inclusive-radar.assistive-technologies.excel', $assistiveTechnology)" class="ms-1"/>
                </div>
                <div class="d-flex gap-2" role="group" aria-label="Ações de gestão">
                    <x-buttons.link-button :href="route('inclusive-radar.assistive-technologies.logs', $assistiveTechnology)" variant="secondary-outline">
                        <i class="fas fa-history"></i> Logs
                    </x-buttons.link-button>
                    <form action="{{ route('inclusive-radar.assistive-technologies.destroy', $assistiveTechnology) }}" method="POST" onsubmit="return confirm('Deseja excluir permanentemente?')">
                        @csrf @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                    <x-buttons.link-button :href="route('inclusive-radar.assistive-technologies.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </footer>
        </main>
    </div>
@endsection
