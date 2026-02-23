@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Cursos' => route('specialized-educational-support.courses.index'),
            $course->name => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Detalhes do Curso</h2>
            <p class="text-muted">Informações estruturais e grade de disciplinas.</p>
        </div>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('specialized-educational-support.courses.edit', $course)" variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>
            <x-buttons.link-button :href="route('specialized-educational-support.courses.index')" variant="secondary">
                Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            <x-forms.section title="Informações Gerais" />

            <x-show.info-item label="Nome do Curso" column="col-md-8" isBox="true">
                <strong>{{ $course->name }}</strong>
            </x-show.info-item>

            <x-show.info-item label="Status" column="col-md-4" isBox="true">
                <span class="text-{{ $course->is_active ? 'success' : 'danger' }} fw-bold">
                    {{ $course->is_active ? 'ATIVO' : 'INATIVO' }}
                </span>
            </x-show.info-item>

            <x-show.info-item label="Descrição / Observações" column="col-md-12" isBox="true">
                {{ $course->description ?? 'Nenhuma descrição informada.' }}
            </x-show.info-item>

            <x-forms.section title="Grade Curricular (Disciplinas)" />

            <div class="col-12 p-4">
                @if($course->disciplines->isEmpty())
                    <p class="text-muted">Nenhuma disciplina vinculada a este curso.</p>
                @else
                    <ul class="list-group">
                        @foreach($course->disciplines as $discipline)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $discipline->name }}
                                <span class="badge bg-purple-dark text-white">Obrigatória</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
