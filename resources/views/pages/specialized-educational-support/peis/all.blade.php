@extends('layouts.master')

@section('title', 'Todos os Planos Educacionais (PEI)')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'PEIs' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">

                Listagem Geral de PEIs
            </h2>
            <p class="text-muted">Visualização consolidada de todos os planos e adaptações curriculares do campus.</p>
        </div>
        {{-- O botão de "Novo" geralmente não fica aqui pois o PEI exige partir de um aluno específico --}}
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body bg-light rounded">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Estatísticas do Semestre</h6>
                    <div class="d-flex gap-4">
                        <div>
                            <small class="d-block text-muted">Total de Planos:</small>
                            <span class="fw-bold fs-5">{{ $peis->count() }}</span>
                        </div>
                        <div>
                            <small class="d-block text-muted">Estudantes com PEI:</small>
                            <span class="fw-bold fs-5">{{ $peis->unique('student_id')->count() }}</span>
                        </div>
                        <div>
                            <small class="d-block text-muted">Aguardando Finalização:</small>
                            <span class="fw-bold fs-5 text-warning">{{ $peis->where('is_finished', false)->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="text-muted small">
                         Semestre Letivo: <br>
                        <span class="fw-bold">{{ $semester->label ?? 'Vigente' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Estudante', 'Semestre', 'Componente Curricular', 'Docente', 'Status', 'Ações']">
        @forelse($peis as $pei)
            <tr>
                <x-table.td>
                    <div class="d-flex align-items-center">
                        
                        <div>
                            <strong class="d-block">{{ $pei->student->person->name }}</strong>
                            <small class="text-muted">Matrícula: {{ $pei->student->registration }}</small>
                        </div>
                    </div>
                </x-table.td>

                <x-table.td>
                    <strong>{{ $pei->semester->label ?? 'N/A' }}</strong>
                </x-table.td>

                <x-table.td>
                    <span class="fw-bold">{{ $pei->discipline->name ?? 'N/A' }}</span><br>
                    <small class="text-muted">{{ $pei->course->name ?? '' }}</small>
                </x-table.td>

                <x-table.td>
                    {{ $pei->teacher_name }}
                </x-table.td>

                <x-table.td>
                    @if($pei->is_finished)
                        <span class=" text-success ">
                           FINALIZADO
                        </span>
                    @else
                        <span class=" text-warning ">
                            EM ABERTO
                        </span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.pei.show', $pei->id)"
                            variant="info"
                            title="Ver Detalhes"
                        >
                           <i class="fas fa-eye" aria-hidden="true"></i> Ver
                        </x-buttons.link-button>
                        <form action="{{ route('specialized-educational-support.pei.destroy', $pei) }}"
                            method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')

                            <x-buttons.submit-button 
                                variant="danger"
                                onclick="return confirm('Deseja remover este pei?')"
                                aria-label="Excluir pei do sistema"
                            >
                            <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                            </x-buttons.submit-button>
                        </form>

                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-5">
                    <i class="fas fa-search d-block mb-2" style="font-size: 2rem;"></i>
                    Nenhum PEI encontrado no sistema.
                </td>
            </tr>
        @endforelse
    </x-table.table>
@endsection