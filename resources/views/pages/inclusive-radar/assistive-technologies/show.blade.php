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
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Nome da Tecnologia" column="col-md-6" isBox="true">
                    <strong>{{ $assistiveTechnology->name }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Categoria / Tipo" column="col-md-6" isBox="true">
                    {{ $assistiveTechnology->type->name ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Descrição Detalhada" column="col-md-12" isBox="true">
                    {!! nl2br(e($assistiveTechnology->description)) ?? '---' !!}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Especificações Técnicas (Atributos Dinâmicos) --}}
            @if(count($attributeValues) > 0)
                <x-forms.section title="Especificações Técnicas" />
                <div class="row g-3 px-4 pb-4">
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

            {{-- SEÇÃO 3: TREINAMENTOS E CAPACITAÇÃO --}}
            <x-forms.section title="Treinamentos e Capacitações" />

            <div class="col-12 mt-4">
                <div class="px-4 mb-4">
                    @if($assistiveTechnology->trainings->count() > 0)
                        {{-- CASO HAJA REGISTROS --}}
                        <div class="p-0 border rounded bg-white shadow-sm overflow-hidden">
                            <x-table.table :headers="['Título', 'Status', 'Ações']">
                                @foreach($assistiveTechnology->trainings as $training)
                                    <tr>
                                        {{-- TÍTULO --}}
                                        <x-table.td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-chalkboard-teacher text-purple me-2"></i>
                                                <span class="fw-bold text-dark">{{ $training->title }}</span>
                                            </div>
                                        </x-table.td>

                                        {{-- STATUS --}}
                                        <x-table.td>
                                <span class="text-{{ $training->is_active ? 'success' : 'secondary' }} fw-bold">
                                    {{ $training->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                                        </x-table.td>

                                        {{-- AÇÕES --}}
                                        <x-table.td>
                                            <x-table.actions>
                                                {{-- Visualizar --}}
                                                <x-buttons.link-button
                                                    :href="route('inclusive-radar.trainings.show', $training)"
                                                    variant="info"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                </x-buttons.link-button>

                                                {{-- Editar --}}
                                                <x-buttons.link-button
                                                    :href="route('inclusive-radar.trainings.edit', $training)"
                                                    variant="warning"
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </x-buttons.link-button>
                                            </x-table.actions>
                                        </x-table.td>
                                    </tr>
                                @endforeach
                            </x-table.table>
                        </div>

                        {{-- Botão Adicionar no canto inferior direito --}}
                        <div class="text-end mt-3">
                            <x-buttons.link-button
                                :href="route('inclusive-radar.trainings.create', ['type' => 'assistive_technology', 'id' => $assistiveTechnology->id])"
                                variant="primary"
                                class="btn-sm shadow-sm"
                            >
                                <i class="fas fa-plus me-1"></i> Adicionar Treinamento
                            </x-buttons.link-button>
                        </div>
                    @else
                        {{-- CASO NÃO HAJA REGISTROS (Vazio) --}}
                        <div class="text-center py-5 border rounded bg-light border-dashed">
                            <i class="fas fa-chalkboard-teacher fa-3x mb-3 text-muted opacity-20"></i>
                            <p class="text-muted italic mb-3">Nenhum treinamento cadastrado para este recurso.</p>

                            <x-buttons.link-button
                                :href="route('inclusive-radar.trainings.create', ['type' => 'assistive_technology', 'id' => $assistiveTechnology->id])"
                                variant="primary"
                                class="shadow-sm"
                            >
                                <i class="fas fa-plus me-1"></i> Adicionar Primeiro Treinamento
                            </x-buttons.link-button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SEÇÃO 4: Histórico de Vistorias --}}
            <x-forms.section title="Histórico de Vistorias" />
            <div class="col-12 mb-4 px-4 pb-4">
                <div class="history-timeline p-4 border rounded bg-light" style="max-height: 450px; overflow-y: auto;">
                    @forelse($assistiveTechnology->inspections->sortByDesc('inspection_date') as $inspection)
                        <div
                            class="inspection-link d-block mb-3"
                            style="cursor:pointer;"
                            onclick="window.location='{{ route('inclusive-radar.assistive-technologies.inspection.show', [$assistiveTechnology, $inspection]) }}'"
                        >
                            <x-forms.inspection-history-card :inspection="$inspection" />
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted bg-white rounded border border-dashed">
                            <i class="fas fa-history fa-3x mb-3 opacity-20"></i>
                            <p class="fw-bold">Nenhum histórico encontrado.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SEÇÃO 5: Gestão e Público --}}
            <x-forms.section title="Gestão e Público" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Quantidade Total" column="col-md-6" isBox="true">
                    {{ $assistiveTechnology->quantity }}
                </x-show.info-item>

                <x-show.info-item label="Status do Recurso" column="col-md-6" isBox="true">
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subsetle px-3">
                        {{ $assistiveTechnology->resourceStatus->name ?? '---' }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Ativo no Sistema" column="col-md-12" isBox="true">
                    {{ $assistiveTechnology->is_active ? 'Sim' : 'Não' }}
                </x-show.info-item>

                <x-show.info-item label="Público-Alvo" column="col-md-12" isBox="true">
                    <div class="tag-container"> {{-- Adicione esta div --}}
                        @forelse($assistiveTechnology->deficiencies->sortBy('name') as $deficiency)
                            <x-show.tag color="light">{{ $deficiency->name }}</x-show.tag>
                        @empty
                            <span class="text-muted">Nenhum público-alvo definido.</span>
                        @endforelse
                    </div>
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i> ID: #{{ $assistiveTechnology->id }}
                    <x-buttons.pdf-button :href="route('inclusive-radar.assistive-technologies.pdf', $assistiveTechnology)" class="ms-3" />
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('inclusive-radar.assistive-technologies.logs', $assistiveTechnology) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-history"></i> Logs
                    </a>

                    <form action="{{ route('inclusive-radar.assistive-technologies.destroy', $assistiveTechnology) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir esta Tecnologia Assistiva?')">
                        @csrf @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('inclusive-radar.assistive-technologies.edit', $assistiveTechnology)" variant="warning">
                        <i class="fas fa-edit"></i> Editar
                    </x-buttons.link-button>

                    <x-buttons.link-button :href="route('inclusive-radar.assistive-technologies.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection
