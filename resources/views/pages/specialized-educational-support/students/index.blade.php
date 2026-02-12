@extends('layouts.master')

@section('title', 'Alunos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => null
        ]" />
    </div>
    <div class="d-flex justify-content-between mb-3">
        <h2 class = "text-title">Alunos</h2>
        <x-buttons.link-button
            :href="route('specialized-educational-support.students.create')"
            variant="new"
        >
             Adicionar Aluno
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Nome','Contato', 'Matrícula', 'Status', 'Ingresso', 'Ações']">
    @foreach($students as $student)
        <tr>
            <x-table.td>
                <div class="name-with-photo">
                    <img src="{{ $student->person->photo_url }}" class="avatar-table">
                    <span class="fw-bold text-purple-dark">{{ $student->person->name }}</span>
                </div>
            </x-table.td>
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
                        :href="route('specialized-educational-support.students.show', $student)"
                        variant="info"
                    >
                        Ver
                    </x-buttons.link-button>

                    <x-buttons.link-button 
                        :href="route('specialized-educational-support.student-context.index', $student)"
                        variant="secondary"
                    >
                        Contextos
                    </x-buttons.link-button>

                    <x-buttons.link-button 
                        :href="route('specialized-educational-support.student-deficiencies.index', $student)"
                        variant="dark"
                    >
                        Deficiências
                    </x-buttons.link-button>

                    <x-buttons.link-button 
                        :href="route('specialized-educational-support.guardians.index', $student)"
                        variant="info"
                    >
                        Responsáveis
                    </x-buttons.link-button>

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
