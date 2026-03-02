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
            <p class="text-muted mb-0">
                Cadastre novos recursos institucionais e realize a vistoria inicial.
            </p>
        </header>
        <div>
            <x-buttons.link-button
                :href="route('inclusive-radar.assistive-technologies.index')"
                variant="secondary"
            >
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card
            action="{{ route('inclusive-radar.assistive-technologies.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            {{-- IDENTIFICAÇÃO --}}
            <x-forms.section title="Identificação do Recurso" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Tipo da Tecnologia"
                    required
                    placeholder="Ex: Cadeira de Rodas Motorizada"
                    :value="old('name')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="is_digital"
                    label="Natureza do Recurso"
                    required
                    :options="[0 => 'Recurso Físico', 1 => 'Recurso Digital']"
                    :selected="old('is_digital', 0)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="asset_code"
                    label="Patrimônio / Tombamento"
                    :value="old('asset_code')"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="notes"
                    label="Descrição"
                    rows="3"
                    :value="old('notes')"
                />
            </div>

            {{-- VISTORIA --}}
            <x-forms.section title="Vistoria Inicial" />

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

            <div class="col-md-6">
                <x-forms.select
                    name="conservation_state"
                    label="Estado de Conservação"
                    required
                    :options="collect(\App\Enums\InclusiveRadar\ConservationState::cases())
                        ->mapWithKeys(fn($item) => [$item->value => $item->label()])"
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
                    label="Parecer Técnico"
                    rows="3"
                    :value="old('inspection_description')"
                />
            </div>

            {{-- GESTÃO --}}
            <x-forms.section title="Gestão e Público" />
             {{-- Coluna da esquerda --}}
            <div class="col-md-6 d-flex flex-column gap-3">
                <x-forms.input
                    name="quantity"
                    label="Quantidade Total"
                    type="number"
                    min="0"
                    :value="old('quantity', 1)"
                />

                <x-forms.checkbox
                    name="is_loanable"
                    label="Permitir Empréstimos"
                    description="Marque se este recurso pode ser emprestado"
                    :checked="old('is_loanable', true)"
                />
            </div>

            {{-- Coluna da direita --}}
            <div class="col-md-6 d-flex flex-column gap-3">
                <x-forms.select
                    name="status"
                    label="Status do Recurso"
                    :options="collect(\App\Enums\InclusiveRadar\ResourceStatus::cases())
                        ->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                    :selected="old('status', \App\Enums\InclusiveRadar\ResourceStatus::AVAILABLE->value)"
                />

                <x-forms.checkbox
                    name="is_active"
                    label="Ativar no Sistema"
                    description="Disponível para visualização e empréstimos"
                    :checked="old('is_active', true)"
                />
            </div>
            {{-- DEFICIÊNCIAS --}}
            <div class="col-md-12 mb-4 mt-4">
                <span class="d-block form-label fw-bold text-purple-dark mb-3">
                    Público-alvo (Deficiências Atendidas)
                </span>

                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light @error('deficiencies') border-danger @enderror">
                    @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->orderBy('name')->get() as $def)
                        <x-forms.checkbox
                            name="deficiencies[]"
                            id="def_{{ $def->id }}"
                            :value="$def->id"
                            :label="$def->name"
                            :checked="is_array(old('deficiencies')) && in_array($def->id, old('deficiencies'))"
                        />
                    @endforeach
                </div>

                @error('deficiencies')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>

            {{-- AÇÕES --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4">
                <x-buttons.link-button
                    :href="route('inclusive-radar.assistive-technologies.index')"
                    variant="secondary"
                >
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit">
                    <i class="fas fa-save me-1"></i> Cadastrar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @vite('resources/js/pages/inclusive-radar/assistive-technologies.js')
@endsection
