@extends('layouts.master')

@section('title', 'Histórico de Contextos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Contextos' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Histórico de Contextos</h2>
            <p class="text-muted">Veja o histórico completo das características comportamentais observadas do aluno(a) {{ $student->person->name }}.</p>
        </div>
        @can('student-context.create')
            @if($contexts->isEmpty())

                {{-- Primeiro contexto --}}
                <x-buttons.link-button
                    href="{{ route('specialized-educational-support.student-context.create', $student->id) }}"
                    class="btn-action new">
                    <i class="fas fa-plus"></i>
                    Adicionar Contexto
                </x-buttons.link-button>

            @else

                {{-- Nova versão --}}
                <x-buttons.link-button
                    href="{{ route('specialized-educational-support.student-context.new-version', $student->id) }}"
                    class="btn-action new">
                    <i class="fas fa-plus"></i>
                    Nova Versão
                </x-buttons.link-button>

            @endif
        @endcan
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body bg-light rounded">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Informações do Aluno</h6>
                    <div class="d-flex gap-4">
                        <div>
                            <small class="d-block text-muted">Status:</small>
                            <span class="badge {{ $student->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ strtoupper($student->status) }}
                            </span>
                        </div>
                        <div>
                            <small class="d-block text-muted">Total de Registros:</small>
                            <span class="fw-bold">{{ $contexts->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    @if($contexts->where('is_current', true)->first())
                        <div class="text-success small fw-bold">
                            <i class="fas fa-check-circle"></i> Contexto Atual Ativo
                        </div>
                    @else
                        <div class="text-warning small fw-bold">
                            <i class="fas fa-exclamation-triangle"></i> Nenhum Contexto Atual
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-table.table :headers="['Data', 'Versão','Tipo de Avaliação', 'Status', 'Ações']">
        @forelse($contexts as $context)
            <tr class="{{ $context->is_current ? 'table-success' : '' }}">
                <x-table.td>
                    <strong>{{ $context->created_at->format('d/m/Y') }}</strong>
                </x-table.td>

                <x-table.td >
                   <strong> v{{ $context->version }}</strong>
                </x-table.td>

                <x-table.td>
                    @php
                        $evaluationTypes = [
                            'initial' => 'Avaliação Inicial',
                            'periodic_review' => 'Revisão Periódica',
                            'pei_review' => 'Revisão PEI',
                            'specific_demand' => 'Demanda Específica'
                        ];
                    @endphp
                    <span class="text-uppercase fw-bold small">{{ $evaluationTypes[$context->evaluation_type] ?? $context->evaluation_type }}</span>
                </x-table.td>

                <x-table.td >
                    @if($context->is_current)
                        <span class="text-success">ATUAL</span>
                    @else
                        <span class="text-muted">HISTÓRICO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.student-context.show', $context)"
                            variant="info"
                        >
                            <i class="fas fa-eye" aria-hidden="true"></i> Ver
                        </x-buttons.link-button>

                        <form action="{{ route('specialized-educational-support.student-context.destroy', $context->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button variant="danger" onclick="return confirm('Excluir este registro permanentemente?')">
                                <i class="fas fa-trash" aria-hidden="true"></i>Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size: 2rem;"></i>
                    Nenhum contexto registrado para este aluno.
                </td>
            </tr>
        @endforelse
    </x-table.table>

    <div class="mt-4">
        <x-buttons.link-button
            :href="route('specialized-educational-support.students.show', $student)"
            variant="secondary"
        >
            <i class="fas fa-arrow-left"></i> Voltar
        </x-buttons.link-button>
    </div>
@endsection