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

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Editar Tecnologia Assistiva</h2>
            <p class="text-muted mb-0">
                Atualizando informações de: <strong>{{ $assistiveTechnology->name }}</strong>
            </p>
        </header>

        <x-buttons.link-button
            :href="route('inclusive-radar.assistive-technologies.show', $assistiveTechnology)"
            variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card
            action="{{ route('inclusive-radar.assistive-technologies.update', $assistiveTechnology) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <x-forms.section title="Identificação do Recurso" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Tipo da Tecnologia"
                    required
                    :value="old('name', $assistiveTechnology->name)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="is_digital"
                    label="Natureza do Recurso"
                    required
                    :options="[0 => 'Recurso Físico', 1 => 'Recurso Digital']"
                    :selected="old('is_digital', $assistiveTechnology->is_digital ? 1 : 0)"
                />
            </div>

            <div class="col-md-6" id="asset_code_container">
                <x-forms.input
                    name="asset_code"
                    label="Patrimônio / Tombamento"
                    :value="old('asset_code', $assistiveTechnology->asset_code)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="notes"
                    label="Descrição"
                    rows="3"
                    :value="old('notes', $assistiveTechnology->notes)"
                />
            </div>

            <x-forms.section title="Nova Vistoria" />

            <div class="col-md-6">
                <x-forms.select
                    name="inspection_type"
                    label="Tipo de Inspeção"
                    required
                    :options="$inspectionTypes"
                    :selected="old('inspection_type', $defaultInspection)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="inspection_date"
                    label="Data da Inspeção"
                    type="date"
                    required
                    :value="old('inspection_date', date('Y-m-d'))"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="conservation_state"
                    label="Estado de Conservação"
                    required
                    :options="$conservationStates"
                    :selected="old('conservation_state', $assistiveTechnology->conservation_state?->value)"
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
                    placeholder="Descreva o motivo da mudança de estado ou detalhes da nova vistoria..."
                    :value="old('inspection_description')"
                />
            </div>

            <x-forms.section title="Gestão e Público" />

            <div class="col-md-6 d-flex flex-column gap-3">
                <x-forms.input
                    name="quantity"
                    label="Quantidade Total"
                    type="number"
                    :value="old('quantity', $assistiveTechnology->quantity)"
                    :min="$activeLoans"
                />

                @if($activeLoans > 0)
                    <div class="alert alert-warning py-2 mb-0">
                        <small class="fw-bold">
                            <i class="fas fa-lock"></i> {{ $activeLoans }} unidades em uso.
                        </small>
                    </div>
                @endif

                <x-forms.checkbox
                    name="is_loanable"
                    label="Permitir Empréstimos"
                    description="Marque se este recurso pode ser emprestado"
                    :checked="old('is_loanable', $assistiveTechnology->is_loanable)"
                />
            </div>

            <div class="col-md-6 d-flex flex-column gap-3">
                <x-forms.select
                    name="status"
                    label="Status do Recurso"
                    :options="$resourceStatuses"
                    :selected="old('status', $assistiveTechnology->status?->value)"
                />

                <x-forms.checkbox
                    name="is_active"
                    label="Ativar no Sistema"
                    description="Disponível para visualização e empréstimos"
                    :checked="old('is_active', $assistiveTechnology->is_active)"
                />
            </div>

            <div class="col-md-12 mb-4 mt-4">
                <span class="d-block form-label fw-bold text-purple-dark mb-3">
                    Público-alvo (Deficiências Atendidas)
                </span>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light @error('deficiencies') border-danger @enderror">
                    @foreach($deficiencies as $def)
                        <x-forms.checkbox
                            name="deficiencies[]"
                            id="def_{{ $def->id }}"
                            :value="$def->id"
                            :label="$def->name"
                            :checked="in_array($def->id, old('deficiencies', $assistiveTechnology->deficiencies->pluck('id')->toArray()))"
                        />
                    @endforeach
                </div>
                @error('deficiencies')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4">
                <x-buttons.link-button
                    :href="route('inclusive-radar.assistive-technologies.show', $assistiveTechnology)"
                    variant="secondary"
                >
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button>
                    <i class="fas fa-save me-1"></i> Salvar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @vite('resources/js/pages/inclusive-radar/assistive-technologies.js')
@endsection
