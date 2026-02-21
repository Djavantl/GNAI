@extends('layouts.master')

@section('title', "$material->name")

@section('content')
    {{-- Cabeçalho --}}
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
            <p class="text-muted mb-0">Visualize as informações cadastrais, histórico de vistorias e gestão do material.</p>
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

            {{-- SEÇÃO 1: Identificação do Recurso --}}
            <x-forms.section title="Identificação do Recurso" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Título do Material" column="col-md-12" isBox="true">
                    <strong>{{ $material->name }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Descrição Detalhada" column="col-md-12" isBox="true">
                    {!! nl2br(e($material->notes)) ?? '---' !!}
                </x-show.info-item>

                <x-show.info-item label="Categoria / Tipo" column="col-md-6" isBox="true" :value="$material->type->name ?? '---'" />

                <x-show.info-item label="Patrimônio / Tombamento" column="col-md-6" isBox="true">
                    <strong>{{ $material->asset_code ?? '---' }}</strong>
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Especificações Técnicas --}}
            @if(count($attributeValues) > 0)
                <x-forms.section title="Especificações Técnicas" />
                <div class="row g-3 px-4 pb-4">
                    @foreach($attributeValues as $attributeId => $value)
                        <x-show.info-item
                            :label="$material->attributeValues->firstWhere('attribute_id', $attributeId)?->attribute->label ?? 'Campo Técnico'"
                            column="col-md-6"
                            isBox="true"
                            :value="$value"
                        />
                    @endforeach
                </div>
            @endif

            {{-- SEÇÃO 3: Recursos de Acessibilidade --}}
            <x-forms.section title="Recursos de Acessibilidade" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Recursos presentes no material" column="col-md-12" isBox="true">
                    @if($material->accessibilityFeatures->isNotEmpty())
                        <div class="tag-container" role="list">
                            @foreach($material->accessibilityFeatures->sortBy('name') as $feature)
                                <x-show.tag color="light" role="listitem">{{ $feature->name }}</x-show.tag>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">Nenhum recurso informado</span>
                    @endif
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 4: TREINAMENTOS --}}
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
                        <div class="text-center py-5 border rounded bg-light border-dashed">
                            <i class="fas fa-chalkboard-teacher fa-3x mb-3 text-muted opacity-20"></i>
                            <p class="text-muted italic mb-3">Nenhum treinamento cadastrado.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SEÇÃO 5: Histórico --}}
            <x-forms.section title="Histórico de Vistorias" />
            <div class="col-12 mb-4 px-4 pb-4">
                <div class="history-timeline p-4 border rounded bg-light" style="max-height: 450px; overflow-y: auto;" role="log">
                    @forelse($material->inspections()->with('images')->latest('inspection_date')->get() as $inspection)
                        <div class="inspection-link d-block mb-3" style="cursor:pointer;" onclick="window.location='{{ route('inclusive-radar.accessible-educational-materials.inspection.show', [$material, $inspection]) }}'" role="link" tabindex="0">
                            <x-forms.inspection-history-card :inspection="$inspection" />
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted bg-white rounded border border-dashed">
                            <p class="fw-bold">Nenhum histórico encontrado.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SEÇÃO 6: Gestão --}}
            <x-forms.section title="Gestão e Público" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Quantidade Total" column="col-md-6" isBox="true" :value="$material->quantity" />
                <x-show.info-item label="Status do Recurso" column="col-md-6" isBox="true">
                    <span class="badge bg-info-subtle text-info-emphasis border px-3">
                        {{ $material->resourceStatus->name ?? '---' }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Status no Sistema" column="col-md-12" isBox="true">
                    <span class="text-{{ $material->is_active ? 'success' : 'secondary' }} fw-bold text-uppercase">
                        {{ $material->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Público-alvo (Deficiências Atendidas)" column="col-md-12" isBox="true">
                    <div class="tag-container" role="list">
                        @forelse($material->deficiencies->sortBy('name') as $deficiency)
                            <x-show.tag color="light" role="listitem">{{ $deficiency->name }}</x-show.tag>
                        @empty
                            <span class="text-muted">Nenhum público-alvo definido.</span>
                        @endforelse
                    </div>
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <footer class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small d-flex align-items-center">
                    <i class="fas fa-id-card me-1" aria-hidden="true"></i> ID no Sistema: #{{ $material->id }}
                    <x-buttons.pdf-button :href="route('inclusive-radar.accessible-educational-materials.pdf', $material)" class="ms-1" />
                </div>
                <div class="d-flex gap-2" role="group" aria-label="Ações de gestão">
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
@endsection
