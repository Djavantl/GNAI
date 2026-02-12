@extends('layouts.master')

@section('title', 'Cursos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Cursos' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <h2 class="text-title">Cursos e Séries</h2>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('specialized-educational-support.disciplines.index')" variant="secondary">
                 Gerenciar Disciplinas
            </x-buttons.link-button>
            <x-buttons.link-button :href="route('specialized-educational-support.courses.create')" variant="new">
                 Adicionar Curso
            </x-buttons.link-button>
        </div>
    </div>

    <x-table.table :headers="['Nome do Curso', 'Disciplinas', 'Status', 'Ações']">
    @foreach($courses as $course)
        <tr>
            <x-table.td>{{ $course->name }}</x-table.td>
            <x-table.td>{{ $course->disciplines_count }} matérias</x-table.td>
            <x-table.td>
                <span class="text-{{ $course->is_active ? 'success' : 'danger' }} fw-bold">
                    {{ $course->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </x-table.td>
            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button :href="route('specialized-educational-support.courses.show', $course)" variant="info">Ver Grade</x-buttons.link-button>
                    <x-buttons.link-button :href="route('specialized-educational-support.courses.edit', $course)" variant="warning">Editar</x-buttons.link-button>
                </x-table.actions>
            </x-table.td>
        </tr>
    @endforeach
    </x-table.table>
@endsection
