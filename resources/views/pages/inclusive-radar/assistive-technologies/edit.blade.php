@extends('layouts.master')

@section('title', "Editar - $assistiveTechnology->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Tecnologias Assistivas' => route('inclusive-radar.assistive-technologies.index'),
            $assistiveTechnology->name => route('inclusive-radar.assistive-technologies.show', $assistiveTechnology),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Tecnologia Assistiva</h2>
            <p class="text-muted">Atualizando informações de: <strong>{{ $assistiveTechnology->name }}</strong></p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">Patrimônio Atual</span>
            <span class="badge bg-purple fs-6">{{ $assistiveTechnology->asset_code ?? 'SEM CÓDIGO' }}</span>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.assistive-technologies.update', $assistiveTechnology->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')

            <x-forms.section title="Identificação do Recurso" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Nome da Tecnologia / Equipamento *"
                    required
                    :value="old('name', $assistiveTechnology->name)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição Detalhada"
                    rows="3"
                    :value="old('description', $assistiveTechnology->description)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="type_id"
                    label="Categoria / Tipo *"
                    id="type_id"
                    required
                    :options="$resourceTypes->pluck('name', 'id')"
                    :selected="$assistiveTechnology->type_id"
                    :resourceObjects="$resourceTypes"
                />
            </div>

            <div class="col-md-6" id="asset_code_container">
                <x-forms.input
                    name="asset_code"
                    label="Patrimônio / Tombamento"
                    :value="old('asset_code', $assistiveTechnology->asset_code)"
                />
            </div>

            {{-- Seção de Especificações Técnicas (Dinâmica via JS) --}}
            <div id="dynamic-attributes-container" style="display: none;">
                <x-forms.section title="Especificações Técnicas" />
                <div class="row g-0" id="dynamic-attributes">
                    {{-- O JS vai preencher aqui --}}
                </div>
            </div>

            {{-- Seção 2: Histórico de Vistorias --}}
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

            <div class="col-md-6" id="conservation_container">
                <x-forms.select
                    name="conservation_state"
                    label="Estado de Conservação Atual *"
                    :options="collect(\App\Enums\InclusiveRadar\ConservationState::cases())->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                    :selected="$assistiveTechnology->conservation_state->value"
                />
            </div>

            <div class="col-md-6">
                <x-forms.image-uploader
                    name="images[]"
                    label="Fotos de Evidência"
                    :existingImages="old('images', $assistiveTechnology->images?->pluck('path')->toArray() ?? [])"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="inspection_description"
                    label="Parecer Técnico / Descrição da Vistoria"
                    rows="3"
                    placeholder="O que mudou no equipamento desde a última vistoria?"
                />
            </div>

            <x-forms.section title="Gestão e Público" />

            <div class="col-md-6" id="quantity_container">
                @php $activeLoans = $assistiveTechnology->loans()->whereIn('status', ['active', 'late'])->count(); @endphp
                <x-forms.input
                    name="quantity"
                    label="Quantidade Total *"
                    type="number"
                    id="quantity_input"
                    :value="old('quantity', $assistiveTechnology->quantity)"
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
                    :options="\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->pluck('name', 'id')"
                    :selected="$assistiveTechnology->status_id"
                />
            </div>

            <div class="col-md-6">
                <x-forms.checkbox
                    name="requires_training"
                    label="Requer Treinamento"
                    description="Indica necessidade de capacitação para uso"
                    :checked="old('requires_training', $assistiveTechnology->requires_training)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    label="Ativar no Sistema"
                    description="Fica visível para empréstimos imediatamente"
                    :checked="old('is_active', $assistiveTechnology->is_active)"
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
                            :checked="in_array($def->id, old('deficiencies', $assistiveTechnology->deficiencies->pluck('id')->toArray()))"
                        />
                    @endforeach
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.assistive-technologies.index') }}" variant="secondary">
                    Cancelar Alterações
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    Salvar Alterações
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>

    {{-- Script necessário para injetar os valores dos atributos já existentes --}}
    <script>
        window.currentAttributeValues = @json($attributeValues ?? []);
    </script>
@endsection
