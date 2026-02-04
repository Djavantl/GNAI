@extends('layouts.master')

@section('title', 'Alunos')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h2 class = "text-title">Alunos</h2>
        <x-buttons.link-button
            :href="route('specialized-educational-support.students.create')"
            variant="new"
        >
             Adicionar Aluno
        </x-buttons.link-button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Nome','Contato', 'Matrícula', 'Status', 'Ingresso', 'Ações']">
    @foreach($students as $student)
        <tr>
            <x-table.td>{{ $student->person->name }}</x-table.td>
            <x-table.td>{{ $student->person->email}}</x-table.td>
            <x-table.td>{{ $student->registration }}</x-table.td>
            <x-table.td>
                @php
                    $statusColor = $student->status === 'active' ? 'success' : 'danger';
                    $statusLabel = $student->status === 'active' ? 'Ativo' : 'Inativo';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold ">
                    {{ $statusLabel }}
                </span>
            </x-table.td>
            <x-table.td>{{ \Carbon\Carbon::parse($student->entry_date)->format('d/m/Y') }}</x-table.td>

            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.students.edit', $student)"
                        variant="warning"
                    >
                        Editar
                    </x-buttons.link-button>

                    <form action="{{ route('specialized-educational-support.students.destroy', $student) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')

                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja excluir este aluno?')"
                        >
                            Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @endforeach
</x-table.table>
</div>
@endsection
