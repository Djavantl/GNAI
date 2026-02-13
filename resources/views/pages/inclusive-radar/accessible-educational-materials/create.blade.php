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

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Novo Material Pedagógico Acessível (MPA)</h2>
            <p class="text-muted">Cadastre materiais adaptados e realize a vistoria inicial para controle de acervo.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.accessible-educational-materials.store') }}" method="POST" enctype="multipart/form-data">

            {{-- SEÇÃO 1: Identificação do Recurso --}}
            <x-forms.section title="Identificação do Recurso" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Título do Material *"
                    required
                    :value="old('name')"
                    placeholder="Ex: Livro em Braille, Maquete Tátil..."
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="notes"
                    label="Descrição Detalhada"
                    rows="3"
                    :value="old('notes')"
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
                    :resourceObjects="$resourceTypes"
                />
            </div>

            <div class="col-md-6" id="asset_code_container">
                <x-forms.input
                    name="asset_code"
                    label="Patrimônio / Tombamento"
                    :value="old('asset_code')"
                />
            </div>

            {{-- SEÇÃO 2: Especificações Técnicas --}}
            <div id="dynamic-attributes-container" style="display: none;">
                <x-forms.section title="Especificações Técnicas" />
                <div class="row g-0" id="dynamic-attributes">
                    {{-- Preenchido via educational-materials.js --}}
                </div>
            </div>

            {{-- SEÇÃO 3: Recursos de Acessibilidade --}}
            <x-forms.section title="Recursos de Acessibilidade" />

            <div class="col-md-12 mb-4">
                <label class="form-label fw-bold text-purple-dark">Recursos presentes no material</label>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
                    @foreach(\App\Models\InclusiveRadar\AccessibilityFeature::where('is_active', true)->orderBy('name', 'asc')->get() as $feature)
                        <x-forms.checkbox
                            name="accessibility_features[]"
                            id="feat_{{ $feature->id }}"
                            :value="$feature->id"
                            :label="$feature->name"
                            class="mb-0"
                            :checked="collect(old('accessibility_features'))->contains($feature->id)"
                        />
                    @endforeach
                </div>
            </div>

            {{-- SEÇÃO 4: Detalhes da Vistoria Inicial --}}
            <x-forms.section title="Detalhes da Vistoria Inicial" />

            <div class="col-md-6">
                <x-forms.select
                    name="inspection_type"
                    label="Tipo de Inspeção *"
                    :options="collect(\App\Enums\InclusiveRadar\InspectionType::cases())->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                    :selected="old('inspection_type')"
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
                    label="Estado de Conservação Inicial *"
                    :options="collect(\App\Enums\InclusiveRadar\ConservationState::cases())->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                    :selected="old('conservation_state', 'novo')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.image-uploader
                    name="images[]"
                    label="Fotos Iniciais do Material"
                    :existingImages="old('images', [])"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="inspection_description"
                    label="Notas da nova atualização (Vistoria)"
                    rows="3"
                    :value="old('inspection_description')"
                    placeholder="Descreva as condições físicas do material na entrada (Vistoria Inicial)"
                />
            </div>

            {{-- SEÇÃO 5: Gestão e Público --}}
            <x-forms.section title="Gestão e Público" />

            <div class="col-md-6" id="quantity_container">
                <x-forms.input
                    name="quantity"
                    label="Quantidade Total *"
                    type="number"
                    id="quantity_input"
                    :value="old('quantity', 1)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="status_id"
                    label="Status do Recurso"
                    :options="\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->where('for_educational_material', true)->pluck('name', 'id')"
                    :selected="old('status_id')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.checkbox
                    name="requires_training"
                    label="Requer Treinamento"
                    description="Indica necessidade de capacitação para uso do material"
                    :checked="old('requires_training')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    label="Ativar no Sistema"
                    description="Material visível para empréstimos imediatamente"
                    :checked="old('is_active', true)"
                />
            </div>

            <div class="col-md-12 mb-4 mt-4">
                <label class="form-label fw-bold text-purple-dark">Público-alvo *</label>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
                    @foreach($deficiencies->sortBy('name') as $def)
                        <x-forms.checkbox
                            name="deficiencies[]"
                            id="def_{{ $def->id }}"
                            :value="$def->id"
                            :label="$def->name"
                            class="mb-0"
                            :checked="collect(old('deficiencies'))->contains($def->id)"
                        />
                    @endforeach
                </div>
            </div>

            {{-- BOTÕES --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.accessible-educational-materials.index') }}" variant="secondary">
                    Voltar para Listagem
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    Finalizar Cadastro
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
