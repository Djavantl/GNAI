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

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Nova Tecnologia Assistiva</h2>
            <p class="text-muted mb-0">Cadastre novos equipamentos e realize a vistoria inicial para garantir a prontidão do recurso.</p>
        </header>
        <div>
            <x-buttons.link-button
                :href="route('inclusive-radar.assistive-technologies.index')"
                variant="secondary"
                label="Cancelar edição e voltar para a lista de tecnologias"
            >
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.assistive-technologies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <x-forms.section title="Identificação do Recurso" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Nome da Tecnologia"
                    required
                    placeholder="Ex: Cadeira de Rodas Motorizada"
                    :value="old('name')"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição Detalhada"
                    rows="3"
                    placeholder="Descreva as principais características e finalidade do item"
                    :value="old('description')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="type_id"
                    label="Categoria / Tipo"
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

            <x-forms.section title="Detalhes da Vistoria Inicial" />

            <div class="col-md-6">
                <x-forms.select
                    name="inspection_type"
                    label="Tipo de Inspeção"
                    required
                    :options="collect(\App\Enums\InclusiveRadar\InspectionType::cases())
                    ->filter(fn($item) => $item !== \App\Enums\InclusiveRadar\InspectionType::MAINTENANCE)
                    ->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                    :selected="old('inspection_type', \App\Enums\InclusiveRadar\InspectionType::INITIAL->value)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="inspection_date"
                    label="Data da Inspeção"
                    type="date"
                    :value="old('inspection_date', date('Y-m-d'))"
                />
            </div>

            <div class="col-md-6" id="conservation_container">
                <x-forms.select
                    name="conservation_state"
                    label="Estado de Conservação"
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
                    label="Quantidade Total"
                    type="number"
                    id="quantity_input"
                    :value="old('quantity', 1)"
                    min="1"
                />
            </div>

            @php
                $availableStatus = \App\Models\InclusiveRadar\ResourceStatus::where('code', 'available')->first();
            @endphp

            <div class="col-md-6">
                {{-- Status bloqueado, mostrando o label correto --}}
                <x-forms.input
                    name="status_display"
                    label="Status do Recurso"
                    :value="$availableStatus->name ?? 'Disponível'"
                    disabled
                />
                <input type="hidden" name="status_id" :value="$availableStatus->id ?? ''">
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
                <span class="d-block form-label fw-bold text-purple-dark mb-3">Público-alvo (Deficiências Atendidas)</span>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light @error('deficiencies') border-danger @enderror">
                    @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->orderBy('name', 'asc')->get() as $def)
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
                <x-buttons.link-button :href="route('inclusive-radar.assistive-technologies.index')" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save me-1"></i> Cadastrar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/assistive-technologies.js')
    @endpush
@endsection
