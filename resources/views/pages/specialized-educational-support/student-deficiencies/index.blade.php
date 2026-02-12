@extends('layouts.master')

@section('title', 'Deficiências do Aluno')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Deficiências' => null
        ]" />
    </div>
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Deficiências do Aluno</h2>
            <p class="text-muted">Aluno: {{ $student->person->name }} </p>
        </div>
        <div class="d-flex gap-2">
            <x-buttons.link-button
                :href="route('specialized-educational-support.students.index')"
                variant="secondary"
            >
                Voltar
            </x-buttons.link-button>

            <x-buttons.link-button
                :href="route('specialized-educational-support.student-deficiencies.create', $student)"
                variant="new"
            >
                Nova Deficiência
            </x-buttons.link-button>
        </div>
    </div>

    <x-table.table :headers="['Deficiência', 'Severidade', 'Recursos de Apoio', 'Ações']">
        @forelse($deficiencies as $deficiency)
            <tr>
                <x-table.td>
                    <strong>{{ $deficiency->deficiency->name }}</strong>
                </x-table.td>

                <x-table.td >
                    @php
                        $severityLabels = ['mild' => 'Leve', 'moderate' => 'Moderada', 'severe' => 'Severa'];
                        $severityColors = ['mild' => 'success', 'moderate' => 'warning', 'severe' => 'danger'];
                    @endphp
                    @if($deficiency->severity)
                        <span class="badge bg-{{ $severityColors[$deficiency->severity] }} text-uppercase" style="font-size: 0.75rem;">
                            {{ $severityLabels[$deficiency->severity] }}
                        </span>
                    @else
                        <span class="text-muted small">Não informada</span>
                    @endif
                </x-table.td>

                <x-table.td >
                    @if($deficiency->uses_support_resources)
                        <span class="text-success font-weight-bold">SIM</span>
                    @else
                        <span class="text-muted">NÃO</span>
                    @endif
                </x-table.td>


                <x-table.td>
                    <x-table.actions>
                        {{-- Botão Ver solicitado --}}
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.student-deficiencies.show', $deficiency)"
                            variant="secondary"
                        >
                            Ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.student-deficiencies.edit', $deficiency)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('specialized-educational-support.student-deficiencies.destroy', $deficiency) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja remover esta deficiência do registro do aluno?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-5">
                    Nenhuma deficiência cadastrada para este aluno.
                </td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
