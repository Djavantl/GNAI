@extends('layouts.master')

@section('title', "Editar - $material->name")

@section('content')
    {{-- Breadcrumb --}}
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Materiais Pedagógicos Acessíveis' => route('inclusive-radar.accessible-educational-materials.index'),
            $material->name => route('inclusive-radar.accessible-educational-materials.show', $material),
            'Editar' => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Editar Material Pedagógico Acessível</h2>
            <p class="text-muted">
                Atualizando informações de:
                <strong>{{ $material->name }}</strong>
            </p>
        </div>

        <x-buttons.link-button
            :href="route('inclusive-radar.accessible-educational-materials.show', $material)"
            variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    {{-- Formulário --}}
    <div class="mt-3">
        <x-forms.form-card
            action="{{ route('inclusive-radar.accessible-educational-materials.update', $material) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            {{-- IDENTIFICAÇÃO --}}
            <x-forms.section title="Identificação do Recurso" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Título do Material"
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
                />
            </div>

            {{-- Patrimônio / Tombamento --}}
            <div class="col-md-6" id="asset_code_container">
                <x-forms.input
                    name="asset_code"
                    label="Patrimônio / Tombamento"
                    :value="old('asset_code', $material->asset_code)"
                />
            </div>

            {{-- Natureza do Recurso --}}
            <div class="col-md-6" id="is_digital_container">
                <x-forms.select
                    name="is_digital"
                    label="Natureza do Recurso"
                    required
                    :options="[0 => 'Recurso Físico', 1 => 'Recurso Digital']"
                    :selected="old('is_digital', $material->is_digital ? 1 : 0)"
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
                            :checked="in_array($feature->id, old('accessibility_features', $material->accessibilityFeatures->pluck('id')->toArray()))"
                        />
                    @endforeach
                </div>

                @error('accessibility_features')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>

            {{-- TREINAMENTOS --}}
            <x-forms.section title="Treinamentos e Capacitações" />

            <div class="col-12 mb-4 px-4">
                @if($material->trainings->count())
                    <div class="border rounded bg-white shadow-sm overflow-hidden">
                        <x-table.table :headers="['Título','Status','Ações']">
                            @foreach($material->trainings as $training)
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

            {{-- HISTÓRICO DE VISTORIAS --}}
            <x-forms.section title="Histórico de Vistorias" />

            <div class="col-12 mb-4 px-4">
                <div class="p-4 border rounded bg-light" style="max-height:400px; overflow-y:auto;">
                    @forelse($material->inspections()->latest('inspection_date')->get() as $inspection)
                        <x-forms.inspection-history-card :inspection="$inspection"/>
                    @empty
                        <div class="text-center py-5 text-muted">
                            Nenhum histórico encontrado.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- NOVA VISTORIA --}}
            <x-forms.section title="Nova Atualização de Estado / Vistoria" />

            <div class="col-md-6" id="conservation_container">
                <x-forms.select
                    name="conservation_state"
                    label="Estado de Conservação Atual"
                    :options="collect(\App\Enums\InclusiveRadar\ConservationState::cases())
                        ->mapWithKeys(fn($item)=>[$item->value=>$item->label()])"
                    :selected="old('conservation_state', $material->conservation_state?->value)"
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

            {{-- GESTÃO E PÚBLICO --}}
            <x-forms.section title="Gestão e Público" />

            @php
                $activeLoans = $material->loans()->whereIn('status',['active','late'])->count();
            @endphp

            <div class="col-md-12 mb-4 mt-4 row d-flex justify-content-between">
                {{-- Coluna da esquerda --}}
                <div class="col-md-5 d-flex flex-column gap-3">
                    <x-forms.input
                        name="quantity"
                        label="Quantidade Total"
                        type="number"
                        :value="old('quantity', $material->quantity)"
                        :min="$activeLoans"
                    />

                    @if($activeLoans > 0)
                        <small class="text-danger fw-bold d-block mt-1">
                            <i class="fas fa-lock"></i>
                            {{ $activeLoans }} unidades em uso (não é possível reduzir)
                        </small>
                    @endif

                    <x-forms.checkbox
                        name="is_loanable"
                        label="Permitir Empréstimos"
                        description="Marque se este recurso pode ser emprestado"
                        :checked="old('is_loanable', $material->is_loanable)"
                    />
                </div>

                {{-- Coluna da direita --}}
                <div class="col-md-5 d-flex flex-column gap-3">
                    <x-forms.select
                        name="status"
                        label="Status do Recurso"
                        :options="collect(\App\Enums\InclusiveRadar\ResourceStatus::cases())
                            ->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                        :selected="old('status', $material->status?->value)"
                    />

                    <x-forms.checkbox
                        name="is_active"
                        label="Ativar no Sistema"
                        description="Disponível para visualização e empréstimos"
                        :checked="old('is_active', $material->is_active)"
                    />
                </div>
            </div>
            {{-- DEFICIÊNCIAS --}}
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
                            :checked="in_array($def->id, old('deficiencies', $material->deficiencies->pluck('id')->toArray()))"
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
                    :href="route('inclusive-radar.accessible-educational-materials.show', $material)"
                    variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @vite('resources/js/pages/inclusive-radar/accessible-educational-materials.js')
@endsection
