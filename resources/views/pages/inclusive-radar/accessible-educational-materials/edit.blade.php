@extends('layouts.master')

@section('title', "Editar - $material->name")

@section('content')
    {{-- Cabeçalho com Breadcrumb --}}
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Materiais Pedagógicos Acessíveis' => route('inclusive-radar.accessible-educational-materials.index'),
            $material->name => route('inclusive-radar.accessible-educational-materials.show', $material),
            'Editar' => null
        ]" />
    </div>

    {{-- Título e Botões de Ação Superiores --}}
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Editar Material Pedagógico Acessível</h2>
            <p class="text-muted mb-0">Atualizando informações de: <strong>{{ $material->name }}</strong></p>
        </header>
        <div role="group" aria-label="Ações principais">
            <x-buttons.link-button
                :href="route('inclusive-radar.accessible-educational-materials.show', $material)"
                variant="secondary"
                label="Cancelar edição e voltar para a lista">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.accessible-educational-materials.update', $material->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @csrf

            {{-- SEÇÃO 1: Identificação do Recurso --}}
            <x-forms.section title="Identificação do Recurso" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Título do Material *"
                    required
                    :value="old('name', $material->name)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="notes"
                    label="Descrição Detalhada"
                    rows="3"
                    :value="old('notes', $material->notes)"
                    placeholder="Descreva as características principais do material..."
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="type_id"
                    label="Categoria / Tipo *"
                    id="type_id"
                    required
                    :options="$resourceTypes->pluck('name', 'id')"
                    :selected="$material->type_id"
                    :resourceObjects="$resourceTypes"
                />
            </div>

            <div class="col-md-6" id="asset_code_container">
                <x-forms.input
                    name="asset_code"
                    label="Patrimônio / Tombamento"
                    :value="old('asset_code', $material->asset_code)"
                />
            </div>

            {{-- SEÇÃO 2: Especificações Técnicas (Dinâmicas) --}}
            <div id="dynamic-attributes-container" style="display: none;">
                <x-forms.section title="Especificações Técnicas" />
                <div class="row g-0" id="dynamic-attributes" aria-live="polite">
                    {{-- Preenchido via JS --}}
                </div>
            </div>

            {{-- SEÇÃO 3: Recursos de Acessibilidade --}}
            <x-forms.section title="Recursos de Acessibilidade" />
            <div class="col-md-12 mb-4">
                <span class="d-block form-label fw-bold text-purple-dark mb-3">Recursos presentes no material</span>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
                    @foreach(\App\Models\InclusiveRadar\AccessibilityFeature::where('is_active', true)->orderBy('name', 'asc')->get() as $feature)
                        <x-forms.checkbox
                            name="accessibility_features[]"
                            id="feat_{{ $feature->id }}"
                            :value="$feature->id"
                            :label="$feature->name"
                            :checked="in_array($feature->id, old('accessibility_features', $material->accessibilityFeatures->pluck('id')->toArray()))"
                        />
                    @endforeach
                </div>
            </div>

            {{-- SEÇÃO 4: Treinamentos --}}
            <x-forms.section title="Treinamentos e Capacitações" />
            <div class="col-12 mt-4">
                <div class="px-4 mb-4">
                    @if($material->trainings->count() > 0)
                        <div class="p-0 border rounded bg-white shadow-sm overflow-hidden">
                            <x-table.table :headers="['Título', 'Status', 'Ações']">
                                @foreach($material->trainings as $training)
                                    <tr>
                                        <x-table.td>
                                            <span class="fw-bold text-dark">{{ $training->title }}</span>
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
                        <div class="text-end mt-3">
                            <x-buttons.link-button
                                :href="route('inclusive-radar.trainings.create', ['type' => 'accessible_educational_material', 'id' => $material->id])"
                                variant="primary" class="btn-sm shadow-sm">
                                <i class="fas fa-plus me-1"></i> Adicionar Treinamento
                            </x-buttons.link-button>
                        </div>
                    @else
                        <div class="text-center py-5 border rounded bg-light border-dashed">
                            <i class="fas fa-chalkboard-teacher fa-3x mb-3 text-muted opacity-20"></i>
                            <p class="text-muted italic mb-3">Nenhum treinamento cadastrado.</p>
                            <x-buttons.link-button
                                :href="route('inclusive-radar.trainings.create', ['type' => 'accessible_educational_material', 'id' => $material->id])"
                                variant="primary">
                                <i class="fas fa-plus me-1"></i> Adicionar Primeiro Treinamento
                            </x-buttons.link-button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SEÇÃO 5: Histórico --}}
            <x-forms.section title="Histórico de Vistorias" />
            <div class="col-12 mb-4 px-4">
                <div class="history-timeline p-4 border rounded bg-light" style="max-height: 450px; overflow-y: auto;">
                    @forelse($material->inspections()->with('images')->latest('inspection_date')->get() as $inspection)
                        <div class="inspection-link d-block mb-3" style="cursor:pointer;" onclick="window.location='{{ route('inclusive-radar.accessible-educational-materials.inspection.show', [$material, $inspection]) }}'">
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

            {{-- SEÇÃO 6: Nova Vistoria --}}
            <x-forms.section title="Nova Atualização de Estado / Vistoria" />
            <div class="col-md-6">
                <x-forms.select
                    name="inspection_type"
                    label="Tipo de Inspeção *"
                    :options="collect(\App\Enums\InclusiveRadar\InspectionType::cases())
                        ->filter(fn($type) => $type !== \App\Enums\InclusiveRadar\InspectionType::INITIAL)
                        ->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="inspection_date"
                    label="Data da Inspeção *"
                    type="date"
                    :value="date('Y-m-d')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="conservation_state"
                    label="Estado de Conservação Atual *"
                    :options="collect(\App\Enums\InclusiveRadar\ConservationState::cases())->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                    :selected="$material->conservation_state?->value"
                />
            </div>

            <div class="col-md-6">
                <x-forms.image-uploader
                    name="images[]"
                    label="Fotos de Evidência"
                    multiple
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="inspection_description"
                    label="Parecer Técnico / Descrição da Vistoria"
                    rows="3"
                    placeholder="Relate eventuais danos ou observações..."
                />
            </div>

            {{-- SEÇÃO 7: Gestão e Público --}}
            <x-forms.section title="Gestão e Público" />
            <div class="col-md-6">
                @php $activeLoans = $material->loans()->whereIn('status', ['active', 'late'])->count(); @endphp
                <x-forms.input
                    name="quantity"
                    label="Quantidade Total *"
                    type="number"
                    :value="old('quantity', $material->quantity)"
                    :min="$activeLoans"
                />
                @if($activeLoans > 0)
                    <small class="text-danger fw-bold d-block mt-1">
                        <i class="fas fa-lock"></i> {{ $activeLoans }} unidades em uso (bloqueio de redução)
                    </small>
                @endif
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="status_id"
                    label="Status do Recurso"
                    :options="\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->where('for_educational_material', true)->pluck('name', 'id')"
                    :selected="$material->status_id"
                />
            </div>

            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    label="Ativar no Sistema"
                    description="Material visível para empréstimos imediatamente"
                    :checked="old('is_active', $material->is_active)"
                />
            </div>

            <div class="col-md-12 mb-4 mt-4">
                <span class="d-block form-label fw-bold text-purple-dark mb-3">Público-alvo *</span>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
                    @foreach($deficiencies as $def)
                        <x-forms.checkbox
                            name="deficiencies[]"
                            id="def_{{ $def->id }}"
                            :value="$def->id"
                            :label="$def->name"
                            :checked="in_array($def->id, old('deficiencies', $material->deficiencies->pluck('id')->toArray()))"
                        />
                    @endforeach
                </div>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button
                    :href="route('inclusive-radar.accessible-educational-materials.show', $material)"
                    variant="secondary"
                    label="Cancelar edição e voltar para a lista">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button
                    type="submit"
                    class="btn-action new submit"
                    label="Salvar as alterações deste material">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    <script>
        window.currentAttributeValues = @json(old('attributes', $attributeValues ?? []));
    </script>
    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/accessible-educational-materials.js')
    @endpush
@endsection
