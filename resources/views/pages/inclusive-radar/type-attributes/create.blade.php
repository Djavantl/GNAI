@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Novo Atributo Personalizado</h2>
            <p class="text-muted">Crie campos específicos que serão preenchidos nos detalhes de cada recurso.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.type-attributes.store') }}" method="POST">
            @csrf

            <x-forms.section title="Identificação do Atributo" />

            <div class="col-md-12">
                <x-forms.input
                    name="label"
                    label="Rótulo de Exibição (Label) *"
                    required
                    :value="old('label')"
                    placeholder="Ex: Versão do Software, Cor do Chassis, Material..."
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="name"
                    label="Nome Técnico (Slug / DB Name) *"
                    required
                    :value="old('name')"
                    placeholder="Ex: versao_software (sem espaços)"
                />
                <small class="text-muted">Use apenas letras minúsculas e sublinhados (_).</small>
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="field_type"
                    label="Tipo de Dado *"
                    required
                    :options="[
                        'string' => 'Texto Curto (String)',
                        'text' => 'Texto Longo (TextArea)',
                        'integer' => 'Número Inteiro',
                        'decimal' => 'Número Decimal',
                        'boolean' => 'Sim/Não (Booleano)',
                        'date' => 'Data'
                    ]"
                    :selected="old('field_type')"
                />
            </div>

            <x-forms.section title="Regras e Configurações" />

            {{-- Coluna: Obrigatoriedade --}}
            <div class="col-md-6 mb-4">
                <div class="p-3 border rounded bg-light h-100">
                    <label class="form-label fw-bold text-purple-dark mb-3 text-uppercase small">Regras de Preenchimento:</label>
                    <input type="hidden" name="is_required" value="0">
                    <x-forms.checkbox
                        name="is_required"
                        id="is_required"
                        label="Este campo é obrigatório?"
                        description="Se marcado, o sistema impedirá o salvamento do recurso sem este valor."
                        :checked="old('is_required')"
                    />
                </div>
            </div>

            {{-- Coluna: Status Ativo --}}
            <div class="col-md-6 mb-4">
                <div class="p-3 border rounded border-success h-100" style="background-color: #f0fdf4;">
                    <label class="form-label fw-bold text-success mb-3 text-uppercase small italic">Status do Atributo:</label>
                    <input type="hidden" name="is_active" value="0">
                    <x-forms.checkbox
                        name="is_active"
                        id="is_active"
                        label="Atributo Ativo para Uso"
                        description="Define se este campo aparecerá nos formulários de cadastro de recursos."
                        :checked="old('is_active', true)"
                    />
                </div>
            </div>

            {{-- Ações --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.type-attributes.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Atributo
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
