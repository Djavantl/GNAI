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
            <h2 class="text-title">Novo Material Pedagógico Acessível</h2>
            <p class="text-muted mb-0">
                Cadastre materiais adaptados e realize a vistoria inicial.
            </p>
        </header>

        <x-buttons.link-button
            :href="route('inclusive-radar.accessible-educational-materials.index')"
            variant="secondary"
        >
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card
            action="{{ route('inclusive-radar.accessible-educational-materials.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            {{-- IDENTIFICAÇÃO --}}
            <x-forms.section title="Identificação do Recurso" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Título do Material"
                    required
                    placeholder="Ex: Livro em Braille, Maquete Tátil..."
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

            {{-- RECURSOS DE ACESSIBILIDADE --}}
            <x-forms.section title="Recursos de Acessibilidade" />

            <div class="col-md-12 mb-4">
                <span class="d-block form-label fw-bold text-purple-dark mb-3">
                    Recursos presentes no material
                </span>

                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light @error('accessibility_features') border-danger @enderror">
                    @foreach(\App\Models\InclusiveRadar\AccessibilityFeature::where('is_active', true)->orderBy('name')->get() as $feature)
                        <x-forms.checkbox
                            name="accessibility_features[]"
                            id="feat_{{ $feature->id }}"
                            :value="$feature->id"
                            :label="$feature->name"
                            :checked="is_array(old('accessibility_features')) && in_array($feature->id, old('accessibility_features'))"
                        />
                    @endforeach
                </div>

                @error('accessibility_features')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
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

            <div class="col-md-6">
                <x-forms.input
                    name="quantity"
                    label="Quantidade Total"
                    type="number"
                    min="1"
                    :value="old('quantity', 1)"
                />
            </div>

            @php
                $availableStatus = \App\Models\InclusiveRadar\ResourceStatus::where('code', 'available')->first();
            @endphp

            <input type="hidden" name="status_id" value="{{ $availableStatus->id ?? '' }}">

            <div class="col-md-6">
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
                    @foreach($deficiencies->sortBy('name') as $def)
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

            {{-- BOTÕES --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4">
                <x-buttons.link-button
                    :href="route('inclusive-radar.accessible-educational-materials.index')"
                    variant="secondary"
                >
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button>
                    <i class="fas fa-save me-1"></i> Cadastrar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
