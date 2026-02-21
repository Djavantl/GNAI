@extends('layouts.master')

@section('title', 'Cadastrar - Material Pedagógico Acessível')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Materiais Pedagógicos Acessíveis' => route('inclusive-radar.accessible-educational-materials.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Novo Material Pedagógico Acessível (MPA)</h2>
            <p class="text-muted mb-0">Cadastre materiais adaptados e realize a vistoria inicial para garantir a prontidão do recurso.</p>
        </header>
        <div>
            <x-buttons.link-button
                :href="route('inclusive-radar.accessible-educational-materials.index')"
                variant="secondary"
                label="Cancelar edição e voltar para a lista"
            >
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.accessible-educational-materials.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <x-forms.section title="Identificação do Recurso" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Título do Material *"
                    required
                    placeholder="Ex: Livro em Braille, Maquete Tátil..."
                    :value="old('name')"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="notes"
                    label="Descrição Detalhada"
                    rows="3"
                    placeholder="Descreva as principais características e finalidade do item"
                    :value="old('notes')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="type_id"
                    label="Categoria / Tipo *"
                    id="type_id"
                    required
                    :options="$resourceTypes->pluck('name', 'id')"
                    :resourceObjects="$resourceTypes"
                    :selected="old('type_id')"
                />
            </div>

            <div class="col-md-6" id="asset_code_container">
                <x-forms.input
                    name="asset_code"
                    label="Patrimônio / Tombamento"
                    :value="old('asset_code')"
                />
            </div>

            {{-- Container para Atributos Dinâmicos --}}
            <div id="dynamic-attributes-container" style="display: none;">
                <x-forms.section title="Especificações Técnicas" />
                <div class="row g-0" id="dynamic-attributes" aria-live="polite"></div>
            </div>

            {{-- Recursos de Acessibilidade --}}
            <x-forms.section title="Recursos de Acessibilidade" />
            <div class="col-md-12 mb-4">
                <span class="d-block form-label fw-bold text-purple-dark mb-3">Recursos presentes no material</span>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light @error('accessibility_features') border-danger @enderror">
                    @foreach(\App\Models\InclusiveRadar\AccessibilityFeature::where('is_active', true)->orderBy('name', 'asc')->get() as $feature)
                        <x-forms.checkbox
                            name="accessibility_features[]"
                            id="feat_{{ $feature->id }}"
                            :value="$feature->id"
                            :label="$feature->name"
                            class="mb-0"
                            :checked="is_array(old('accessibility_features')) && in_array($feature->id, old('accessibility_features'))"
                        />
                    @endforeach
                </div>
                @error('accessibility_features')
                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                @enderror
            </div>

            <x-forms.section title="Detalhes da Vistoria Inicial" />

            <div class="col-md-6">
                <x-forms.select
                    name="inspection_type"
                    label="Tipo de Inspeção *"
                    required
                    :options="collect(\App\Enums\InclusiveRadar\InspectionType::cases())
                        ->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                    :selected="old('inspection_type', \App\Enums\InclusiveRadar\InspectionType::INITIAL->value)"
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
                    label="Estado de Conservação *"
                    required
                    :options="collect(\App\Enums\InclusiveRadar\ConservationState::cases())->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                    :selected="old('conservation_state')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.image-uploader
                    name="images[]"
                    label="Fotos de Evidência"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="inspection_description"
                    label="Parecer Técnico / Descrição da Vistoria"
                    rows="3"
                    placeholder="Descreva as condições físicas e funcionais do item na entrada"
                    :value="old('inspection_description')"
                />
            </div>

            <x-forms.section title="Gestão e Público" />

            <div class="col-md-6" id="quantity_container">
                <x-forms.input
                    name="quantity"
                    label="Quantidade Total *"
                    type="number"
                    id="quantity_input"
                    :value="old('quantity', 1)"
                    min="1"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="status_id"
                    label="Status do Recurso"
                    required
                    :options="\App\Models\InclusiveRadar\ResourceStatus::active()->forEducationalMaterial()->pluck('name', 'id')"
                    :selected="old('status_id')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    label="Ativar no Sistema"
                    description="Fica disponível para visualização e empréstimos"
                    :checked="old('is_active', true)"
                />
            </div>

            <div class="col-md-12 mb-4 mt-4">
                <span class="d-block form-label fw-bold text-purple-dark mb-3">Público-alvo (Deficiências Atendidas) *</span>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light @error('deficiencies') border-danger @enderror">
                    @foreach($deficiencies->sortBy('name') as $def)
                        <x-forms.checkbox
                            name="deficiencies[]"
                            id="def_{{ $def->id }}"
                            :value="$def->id"
                            :label="$def->name"
                            class="mb-0"
                            :checked="is_array(old('deficiencies')) && in_array($def->id, old('deficiencies'))"
                        />
                    @endforeach
                </div>
                @error('deficiencies')
                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                @enderror
            </div>

            {{-- Ações --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4">
                <x-buttons.link-button :href="route('inclusive-radar.accessible-educational-materials.index')" variant="secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save me-1"></i> Cadastrar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    <script>
        window.currentAttributeValues = @json(old('attributes', []));
    </script>
    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/accessible-educational-materials.js')
    @endpush
@endsection
