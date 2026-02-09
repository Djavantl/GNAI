@extends('layouts.master')

@section('title', "Editar - $barrierCategory->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Categorias de Barreiras' => route('inclusive-radar.barrier-categories.index'),
            $barrierCategory->name => route('inclusive-radar.barrier-categories.show', $barrierCategory),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Categoria de Barreira</h2>
            <p class="text-muted">Atualizando as definições da categoria: <strong>{{ $barrierCategory->name }}</strong></p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.barrier-categories.update', $barrierCategory) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- SEÇÃO 1: Identificação --}}
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

            {{-- SEÇÃO 2: Configurações --}}
            <x-forms.section title="Status e Visibilidade" />

            {{-- Status Ativo: Estilo Limpo TA (Sem divs extras) --}}
            <div class="col-md-12">
                <x-forms.checkbox
                    name="is_active"
                    id="is_active"
                    label="Ativar no Sistema"
                    description="Indica se esta categoria estará disponível para seleção no cadastro de novas barreiras"
                    :checked="old('is_active', $barrierCategory->is_active)"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.barrier-categories.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Alterações
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
