@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-title">Detalhes da Disciplina</h2>
        <x-buttons.link-button :href="route('specialized-educational-support.disciplines.index')" variant="secondary">Voltar</x-buttons.link-button>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            <x-forms.section title="Dados da Disciplina" />

            <x-show.info-item label="Nome" column="col-md-8" isBox="true">
                {{ $discipline->name }}
            </x-show.info-item>

            <x-show.info-item label="Status" column="col-md-4" isBox="true">
                {{ $discipline->is_active ? 'Ativo' : 'Inativo' }}
            </x-show.info-item>

            <x-show.info-item label="Descrição" column="col-md-12" isBox="true">
                {{ $discipline->description ?? 'Sem descrição.' }}
            </x-show.info-item>
        </div>
    </div>
@endsection
