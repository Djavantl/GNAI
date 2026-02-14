@extends('layouts.master')

@section('title', 'Cadastrar - Tecnologia Assistiva')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Tecnologias Assistivas' => route('inclusive-radar.assistive-technologies.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Nova Tecnologia Assistiva</h2>
            <p class="text-muted">Cadastre novos equipamentos e realize a vistoria inicial para garantir a prontidão do recurso.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.assistive-technologies.store') }}" method="POST" enctype="multipart/form-data">

            <x-forms.section title="Identificação do Recurso" />

            <div class="col-md-12">
                <x-forms.input name="name" label="Nome da Tecnologia / Equipamento *" required placeholder="Ex: Cadeira de Rodas Motorizada" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição Detalhada"
                    rows="3"
                    placeholder="Descreva o item"
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
                <x-forms.input name="asset_code" label="Patrimônio / Tombamento" />
            </div>

            <div id="dynamic-attributes-container" style="display: none;">
                <x-forms.section title="Especificações Técnicas" />
                <div class="row g-0" id="dynamic-attributes">
                </div>
            </div>

            <x-forms.section title="Detalhes da Vistoria Inicial" />

            <div class="col-md-6">
                <x-forms.select
                    name="inspection_type"
                    label="Tipo de Inspeção *"
                    :options="collect(\App\Enums\InclusiveRadar\InspectionType::cases())
            ->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                />
            </div>
            <div class="col-md-6">
                <x-forms.input name="inspection_date" label="Data da Inspeção *" type="date" :value="date('Y-m-d')" />
            </div>

            <div class="col-md-6" id="conservation_container">
                <x-forms.select
                    name="conservation_state"
                    label="Estado de Conservação *"
                    :options="collect(\App\Enums\InclusiveRadar\ConservationState::cases())->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                />
            </div>
            <div class="col-md-6">
                <x-forms.image-uploader
                    name="images[]"
                    label="Fotos de Evidência"
                    :existingImages="old('images', [])"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="inspection_description"
                    label="Parecer Técnico / Descrição da Vistoria"
                    rows="3"
                    placeholder="Descreva as condições físicas e funcionais do item na entrada"
                />
            </div>

            <x-forms.section title="Gestão e Público" />

            <div class="col-md-6" id="quantity_container">
                <x-forms.input name="quantity" label="Quantidade Total *" type="number" id="quantity_input" value="1" />
            </div>
            <div class="col-md-6">
                <x-forms.select
                    name="status_id"
                    label="Status do Recurso"
                    :options="\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->pluck('name', 'id')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.checkbox
                    name="requires_training"
                    label="Requer Treinamento"
                    description="Indica necessidade de capacitação para uso"
                    :checked="old('requires_training')"
                />
            </div>
            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    label="Ativar no Sistema"
                    description="Fica visível para empréstimos imediatamente"
                    :checked="old('is_active', true)"
                />
            </div>

            <div class="col-md-12 mb-4 mt-4">
                <label class="form-label fw-bold text-purple-dark">Público-alvo *</label>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
                    @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->orderBy('name', 'asc')->get() as $def)
                        <x-forms.checkbox
                            name="deficiencies[]"
                            id="def_{{ $def->id }}"
                            :value="$def->id"
                            :label="$def->name"
                            class="mb-0"
                        />
                    @endforeach
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.assistive-technologies.index') }}" variant="secondary">
                    <i class="fas fa-arrow-left"></i> Voltar para Listagem
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Finalizar Cadastro
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/assistive-technologies.js')
    @endpush
@endsection
