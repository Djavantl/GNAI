@extends('layouts.master')

@section('title', 'Gestão de Matrículas')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h2 class="text-title">Matrículas e Histórico Geral</h2>
        <x-buttons.link-button :href="route('specialized-educational-support.student-courses.create')" variant="new">
             Nova Matrícula
        </x-buttons.link-button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Aluno', 'Curso / Série', 'Ano Letivo', 'Vigente', 'Status', 'Ações']">
    @foreach($studentCourses as $enrollment)
        <tr>
            <x-table.td>{{ $enrollment->student->person->name }}</x-table.td>
            <x-table.td>{{ $enrollment->course->name }}</x-table.td>
            <x-table.td>{{ $enrollment->academic_year }}</x-table.td>
            <x-table.td>
                @if($enrollment->is_current)
                    <span class="badge bg-success">SIM</span>
                @else
                    <span class="badge bg-light text-dark">NÃO</span>
                @endif
            </x-table.td>
            <x-table.td>
                @php
                    $statusLabel = [
                        'active' => 'Ativo',
                        'completed' => 'Concluído',
                        'dropped' => 'Evadido'
                    ];
                @endphp
                <span class="fw-bold">{{ $statusLabel[$enrollment->status] ?? $enrollment->status }}</span>
            </x-table.td>

            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button :href="route('specialized-educational-support.student-courses.show', $enrollment)" variant="info">
                        Ver
                    </x-buttons.link-button>

                    <x-buttons.link-button :href="route('specialized-educational-support.student-courses.edit', $enrollment)" variant="warning">
                        Editar
                    </x-buttons.link-button>

                    <form action="{{ route('specialized-educational-support.student-courses.destroy', $enrollment) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger" onclick="return confirm('Excluir esta matrícula do histórico?')">
                            Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @endforeach
    </x-table.table>
@endsection
