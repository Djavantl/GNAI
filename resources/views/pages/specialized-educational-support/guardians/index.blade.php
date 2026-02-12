@extends('layouts.master')

@section('title', 'Responsáveis do Aluno')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Responsáveis' => null
        ]" />
    </div>


    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Responsáveis — {{ $student->person->name }}</h2>
            <p class="text-muted">Gerenciamento de vínculos familiares e contatos de emergência.</p>
        </div>
        <div class="d-flex gap-2 align-items-start">
            <x-buttons.link-button
                :href="route('specialized-educational-support.students.index')"
                variant="secondary"
            >
                Voltar para Alunos
            </x-buttons.link-button>

            <x-buttons.link-button
                :href="route('specialized-educational-support.guardians.create', $student)"
                variant="new"
            >
                Novo Responsável
            </x-buttons.link-button>
        </div>
    </div>


    <x-table.table :headers="['Nome', 'Documento', 'Vínculo (Parentesco)', 'Contato', 'Ações']">
        @forelse($guardians as $guardian)
            <tr>
                <x-table.td>
                   {{ $guardian->person->name }}
                </x-table.td>

                <x-table.td>
                    {{ $guardian->person->document }}
                </x-table.td>

                <x-table.td>
                        {{ ucfirst($guardian->relationship) }}
                </x-table.td>

                <x-table.td>
                        {{ $guardian->person->email }}
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.guardians.show', $guardian)"
                            variant="info"
                        >
                            ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.guardians.edit', [$student, $guardian])"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('specialized-educational-support.guardians.destroy', [$student, $guardian]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja remover este vínculo de responsabilidade?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">Nenhum responsável cadastrado para este aluno.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection