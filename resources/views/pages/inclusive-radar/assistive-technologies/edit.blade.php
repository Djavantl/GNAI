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
        <div>
            <h2 class="text-title">Editar Tecnologia Assistiva</h2>
            <p class="text-muted">
                Atualizando informações de:
                <strong>{{ $assistiveTechnology->name }}</strong>
            </p>
        </div>

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
            @method('PUT')
            @csrf

            {{-- IDENTIFICAÇÃO --}}
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
                    :options="[
                        0 => 'Recurso Físico',
                        1 => 'Recurso Digital'
                    ]"
                    :selected="old('is_digital', $assistiveTechnology->is_digital)"
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
                    label="notes"
                    rows="3"
                    :value="old('notes', $assistiveTechnology->notes)"
                />
            </div>

            {{-- TREINAMENTOS --}}
            <x-forms.section title="Treinamentos e Capacitações" />

            <div class="col-12 mt-4 px-4 mb-4">
                @if($assistiveTechnology->trainings->count() > 0)
                    <div class="border rounded bg-white shadow-sm overflow-hidden">
                        <x-table.table :headers="['Título', 'Status', 'Ações']">
                            @foreach($assistiveTechnology->trainings as $training)
                                <tr>
                                    <x-table.td>
                                        <strong>{{ $training->title }}</strong>
                                    </x-table.td>

                                    <x-table.td>
                                        <span class="text-{{ $training->is_active ? 'success' : 'secondary' }} fw-bold text-uppercase">
                                            {{ $training->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </x-table.td>

                                    <x-table.td>
                                        <x-buttons.link-button
                                            :href="route('inclusive-radar.trainings.show', $training)"
                                            variant="info">
                                            <i class="fas fa-eye"></i> Ver
                                        </x-buttons.link-button>
                                    </x-table.td>
                                </tr>
                            @endforeach
                        </x-table.table>
                    </div>
                @else
                    <div class="text-center py-5 border rounded bg-light border-dashed">
                        <p class="text-muted">Nenhum treinamento vinculado.</p>
                    </div>
                @endif
            </div>

            {{-- NOVA VISTORIA --}}
            <x-forms.section title="Nova Atualização de Estado / Vistoria" />

            <div class="col-md-6">
                <x-forms.select
                    name="inspection_type"
                    label="Tipo de Inspeção"
                    required
                    :options="collect(\App\Enums\InclusiveRadar\InspectionType::cases())
                        ->filter(fn($type) => !in_array($type, [
                            \App\Enums\InclusiveRadar\InspectionType::INITIAL,
                            \App\Enums\InclusiveRadar\InspectionType::MAINTENANCE
                        ]))
                        ->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                    :selected="old('inspection_type')"
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
                    label="Estado de Conservação Atual"
                    :options="collect(\App\Enums\InclusiveRadar\ConservationState::cases())
                        ->mapWithKeys(fn($item) => [$item->value => $item->label()])"
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
                />
            </div>

            {{-- GESTÃO --}}
            <x-forms.section title="Gestão e Público" />

            <div class="col-md-12 mb-4 mt-4 row d-flex justify-content-between">
                {{-- Coluna da esquerda --}}
                <div class="col-md-5 d-flex flex-column gap-3">
                    <x-forms.input
                        name="quantity"
                        label="Quantidade Total"
                        type="number"
                        min="0"
                        :value="old('quantity', $assistiveTechnology->quantity)"
                    />

                    <x-forms.checkbox
                        name="is_loanable"
                        label="Permitir Empréstimos"
                        description="Marque se este recurso pode ser emprestado"
                        :checked="old('is_loanable', $assistiveTechnology->is_loanable)"
                    />
                </div>

                {{-- Coluna da direita --}}
                <div class="col-md-5 d-flex flex-column gap-3">
                    <x-forms.select
                        name="status"
                        label="Status do Recurso"
                        :options="collect(\App\Enums\InclusiveRadar\ResourceStatus::cases())
                            ->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                        :selected="old('status', $assistiveTechnology->status?->value)"
                    />

                    <x-forms.checkbox
                        name="is_active"
                        label="Ativar no Sistema"
                        description="Disponível para visualização e empréstimos"
                        :checked="old('is_active', $assistiveTechnology->is_active)"
                    />
                </div>
            </div>
            {{-- DEFICIÊNCIAS --}}
            <div class="col-md-12 mb-4 mt-4">
                <span class="d-block form-label fw-bold text-purple-dark mb-3">
                    Público-alvo (Deficiências Atendidas)
                </span>

                @php
                    // Pega os IDs das deficiências selecionadas
                    $selectedDeficiencies = old('deficiencies', $assistiveTechnology->deficiencies->pluck('id')->toArray());
                @endphp

                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light @error('deficiencies') border-danger @enderror">
                    @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->orderBy('name')->get() as $def)
                        <x-forms.checkbox
                            name="deficiencies[]"
                            id="def_{{ $def->id }}"
                            :value="$def->id"
                            :label="$def->name"
                            :checked="in_array($def->id, $selectedDeficiencies)"
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
                    :href="route('inclusive-radar.assistive-technologies.show', $assistiveTechnology)"
                    variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
    @vite('resources/js/pages/inclusive-radar/assistive-technologies.js')
@endsection
