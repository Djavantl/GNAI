@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Categoria de Barreira</h2>
            <p class="text-muted">Atualize as definições e a visibilidade da categoria selecionada.</p>
        </div>
        <div class="align-self-center">
            <span class="badge bg-light text-muted border py-2 px-3">
                <i class="fas fa-fingerprint me-1"></i> ID: #{{ $barrierCategory->id }}
            </span>
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
        <x-forms.form-card action="{{ route('inclusive-radar.barrier-categories.update', $barrierCategory) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- SEÇÃO 1: Identificação da Categoria --}}
            <x-forms.section title="Informações da Categoria" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Nome da Categoria *"
                    required
                    :value="old('name', $barrierCategory->name)"
                    placeholder="Ex: Arquitetônica, Atitudinal, Comunicacional..."
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição Detalhada"
                    rows="4"
                    :value="old('description', $barrierCategory->description)"
                    placeholder="Descreva o que este tipo de barreira engloba..."
                />
            </div>

            {{-- SEÇÃO 2: Configurações de Status --}}
            <x-forms.section title="Status e Visibilidade" />

            <div class="col-md-12 mb-4">
                <div class="p-3 border rounded bg-light">
                    <x-forms.checkbox
                        name="is_active"
                        id="is_active"
                        label="Categoria Ativa"
                        description="Indica se esta categoria estará disponível para seleção no cadastro de novas barreiras"
                        :checked="old('is_active', $barrierCategory->is_active)"
                    />
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.barrier-categories.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Atualizar Categoria
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
