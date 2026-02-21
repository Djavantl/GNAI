@extends('layouts.master')

@section('title', "Editar - $assignment->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Vínculos de Atributos' => route('inclusive-radar.type-attribute-assignments.index'),
            $assignment->name => route('inclusive-radar.type-attribute-assignments.show', $assignment),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Gerenciar Atributos do Tipo</h2>
            <p class="text-muted">Configurando campos para: <strong>{{ $assignment->name }}</strong></p>
        </div>
        <div>
            <x-buttons.link-button href="{{ route('inclusive-radar.type-attribute-assignments.show', $assignment) }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.type-attribute-assignments.update', $assignment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="type_id" value="{{ $assignment->id }}">

            {{-- SEÇÃO 1: Atribuição de Atributos --}}
            <x-forms.section title="Campos do Formulário" />

            <div class="col-md-12 mb-2">
                <label class="form-label fw-bold text-purple-dark px-2">
                    Marque os atributos técnicos que devem estar disponíveis para este tipo de recurso:
                </label>

                <div class="d-flex flex-wrap gap-2 p-3 border rounded bg-light mx-2">
                    @forelse($attributes as $attribute)
                        <div class="bg-white border rounded p-3 d-flex align-items-center" style="min-width: 200px; flex: 1;">
                            <x-forms.checkbox
                                name="attribute_ids[]"
                                id="attr_{{ $attribute->id }}"
                                :value="$attribute->id"
                                :label="$attribute->label"
                                class="mb-0 fw-bold"
                                :checked="in_array($attribute->id, $assignedAttributeIds)"
                            />
                        </div>
                    @empty
                        <div class="col-12 text-center py-4">
                            <p class="text-muted italic">Nenhum atributo disponível para configuração.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SEÇÃO 2: Aviso de Impacto (Estilo Padronizado) --}}
            <div class="col-12 px-2 mt-2">
                <div class="p-3 border-start border-warning bg-light mx-2" style="border-width: 4px !important;">
                    <p class="text-muted mb-0" style="font-size: 0.85rem; line-height: 1.4;">
                        <span class="text-warning fw-bold">Aviso:</span> Ao desmarcar um atributo, ele deixará de aparecer imediatamente nos formulários de cadastro e edição de todos os recursos vinculados a este tipo (<strong>{{ $assignment->name }}</strong>).
                        Os dados já salvos anteriormente não serão excluídos, mas ficarão ocultos na interface de edição.
                    </p>
                </div>
            </div>

            {{-- Botões de Ação --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.type-attribute-assignments.show', $assignment) }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
