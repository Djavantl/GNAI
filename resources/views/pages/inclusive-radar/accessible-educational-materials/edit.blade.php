@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Material Pedagógico Acessível</h2>
            <p class="text-muted">Atualizando informações de: <strong>{{ $material->name }}</strong></p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID do Registro</span>
            <span class="badge bg-purple fs-6">#{{ $material->id }}</span>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.accessible-educational-materials.update', $material->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')

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

            {{-- SEÇÃO 2: Especificações Técnicas --}}
            <div id="dynamic-attributes-container" style="display: none;">
                <x-forms.section title="Especificações Técnicas" />
                <div class="row g-0" id="dynamic-attributes">
                    {{-- JS irá preencher --}}
                </div>
            </div>

            {{-- SEÇÃO 3: Recursos de Acessibilidade --}}
            <x-forms.section title="Recursos de Acessibilidade" />

            <div class="col-md-12 mb-4">
                <label class="form-label fw-bold text-purple-dark">Recursos presentes no material</label>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
                    @foreach(\App\Models\InclusiveRadar\AccessibilityFeature::where('is_active', true)->get() as $feature)
                        <x-forms.checkbox
                            name="accessibility_features[]"
                            id="feat_{{ $feature->id }}"
                            :value="$feature->id"
                            :label="$feature->name"
                            class="mb-0"
                            :checked="in_array($feature->id, old('accessibility_features', $material->accessibilityFeatures->pluck('id')->toArray()))"
                        />
                    @endforeach
                </div>
            </div>

            {{-- SEÇÃO 4: Histórico de Vistorias --}}
            <x-forms.section title="Histórico de Vistorias" />

            <div class="col-12 mb-4 px-4">
                <div class="history-timeline p-4 border rounded bg-light" style="max-height: 450px; overflow-y: auto;">
                    @forelse($material->inspections()->with('images')->latest('inspection_date')->get() as $inspection)
                        <x-forms.inspection-history-card :inspection="$inspection" />
                    @empty
                        <div class="text-center py-5 text-muted bg-white rounded border border-dashed">
                            <i class="fas fa-history fa-3x mb-3 opacity-20"></i>
                            <p class="fw-bold">Nenhuma vistoria registrada anteriormente.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SEÇÃO 5: Detalhes da Vistoria --}}
            <x-forms.section title="Detalhes da Vistoria" />

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
                    :value="old('inspection_date', date('Y-m-d'))"
                />
            </div>

            <div class="col-md-6" id="conservation_container">
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
                    label="Adicionar Novas Fotos"
                    :existingImages="old('images', $material->images?->pluck('path')->toArray() ?? [])"
                    multiple
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="inspection_description"
                    label="Notas da nova atualização (Vistoria)"
                    rows="3"
                    placeholder="Descreva o motivo da atualização ou o estado atual do material"
                />
            </div>

            {{-- SEÇÃO 6: Gestão e Público --}}
            <x-forms.section title="Gestão e Público" />

            <div class="col-md-6" id="quantity_container">
                @php $activeLoans = $material->loans()->whereIn('status', ['active', 'late'])->count(); @endphp
                <x-forms.input
                    name="quantity"
                    label="Quantidade Total *"
                    type="number"
                    id="quantity_input"
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
                    name="requires_training"
                    label="Requer Treinamento"
                    description="Indica necessidade de capacitação para uso do material"
                    :checked="old('requires_training', $material->requires_training)"
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
                <label class="form-label fw-bold text-purple-dark">Público-alvo *</label>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
                    @foreach($deficiencies as $def)
                        <x-forms.checkbox
                            name="deficiencies[]"
                            id="def_{{ $def->id }}"
                            :value="$def->id"
                            :label="$def->name"
                            class="mb-0"
                            :checked="in_array($def->id, old('deficiencies', $material->deficiencies->pluck('id')->toArray()))"
                        />
                    @endforeach
                </div>
            </div>

            {{-- BOTÕES --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.accessible-educational-materials.index') }}" variant="secondary">
                    Cancelar Alterações
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    Salvar Alterações
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>

    {{-- Script para injetar atributos existentes --}}
    <script>
        window.currentAttributeValues = @json(old('attributes', $attributeValues ?? []));
    </script>
@endsection
