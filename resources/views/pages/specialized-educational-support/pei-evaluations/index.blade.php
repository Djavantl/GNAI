@extends('layouts.master')

@section('title', 'Avaliações do PEI')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $pei->student->person->name => route('specialized-educational-support.students.show', $pei->student),
            'PEIs' => route('specialized-educational-support.pei.index', $pei->student),
            'Avaliações do PEI' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Avaliações do PEI</h2>
            <p class="text-muted">
                Disciplina: {{ $pei->discipline->name ?? 'Não informada' }}
                • Semestre: {{ $pei->semester->label ?? 'N/A' }}
            </p>
        </div>
        <div>
            <x-buttons.link-button
                class="me-3"
                :href="route('specialized-educational-support.pei.show', $pei)"
                variant="secondary"
            >
                <i class="fas fa-arrow-left"></i> Voltar para PEI
            </x-buttons.link-button>

            <x-buttons.link-button
                :href="route('specialized-educational-support.pei-evaluation.create', $pei)"
                variant="new"
            >
                <i class="fas fa-plus"></i> Nova Avaliação
            </x-buttons.link-button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <x-table.table :headers="['Tipo', 'Semestre', 'Profissional', 'Data', 'Ações']">
        @forelse($pei_evaluations as $evaluation)
            <tr>

                <x-table.td>
                    <strong>{{ $evaluation->evaluation_type->label() }}</strong>
                </x-table.td>

                <x-table.td>
                    {{ $evaluation->semester->label ?? 'N/A' }}
                </x-table.td>

                <x-table.td>
                    {{ $evaluation->professional->person->name ?? 'Não informado' }}
                </x-table.td>

                <x-table.td>
                    {{ $evaluation->evaluation_date?->format('d/m/Y') ?? '---' }}
                </x-table.td>

                <x-table.td>
                    <x-table.actions>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.pei-evaluation.show', $evaluation)"
                            variant="info"
                        >
                            <i class="fas fa-eye"></i> Ver
                        </x-buttons.link-button>

                        <form action="{{ route('specialized-educational-support.pei-evaluation.destroy', $evaluation) }}" method="POST" onsubmit="return confirm('Deseja excluir permanentemente?')">
                            @csrf @method('DELETE')
                            <x-buttons.submit-button variant="danger">
                                <i class="fas fa-trash-alt"></i> Excluir
                            </x-buttons.submit-button>
                        </form>

                    </x-table.actions>
                </x-table.td>

            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-clipboard-check d-block mb-2" style="font-size: 2rem;"></i>
                    Nenhuma avaliação registrada para este PEI.
                </td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
