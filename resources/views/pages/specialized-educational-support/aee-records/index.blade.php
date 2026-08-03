@extends('layouts.master')

@section('title', 'Atendimentos AEE')

@section('content')
     <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Atendimentos AEE' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Registros de atendimentos AEE</h2>
            <p class="text-muted">Acompanhamento dos atendimentos educacionais especializados.</p>
        </div>
        <div class="d-flex gap-2 align-items-start">
            {{-- Se houver um ID de agendamento no filtro, permite criar novo registro para AQUELE agendamento --}}
            @if(request()->has('session_id'))
                <x-buttons.link-button
                    :href="route('specialized-educational-support.sessions.index')"
                    variant="secondary"
                >
                    Voltar para Agendamentos
                </x-buttons.link-button>

                {{-- Corrigido: Rota agora espera o parâmetro {session} conforme definido nas rotas --}}
                <x-buttons.link-button
                    :href="route('specialized-educational-support.aee-records.create', request('session_id'))"
                    variant="new"
                >
                    Novo Atendimento AEE
                </x-buttons.link-button>
            @endif
        </div>
    </div>


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="[
        ['label' => 'Data', 'responsive' => false],
        'Alunos no atendimento AEE',
        'Duração',
        ['label' => 'Ações', 'responsive' => false],
    ]">
        @forelse($aeeRecords as $record)
            <tr>
                <x-table.td :responsive="false">
                    {{ $record->attendanceSession->session_date->format('d/m/Y') }}
                </x-table.td>

                <x-table.td>
                    {{-- Agora mostra todos os alunos avaliados neste registro --}}
                    @if($record->studentEvaluations->isNotEmpty())
                        @foreach($record->studentEvaluations as $evaluation)
                            <div class="mb-1">
                                <i class="fas fa-user-grad small text-muted"></i> 
                                <strong>{{ $evaluation->student->person->name }}</strong>
                                @if(!$evaluation->is_present)
                                    <span class="badge bg-danger p-1" style="font-size: 0.6rem;">FALTA</span>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <span class="text-warning small">Nenhum aluno avaliado</span>
                    @endif
                </x-table.td>

                <x-table.td >
                        {{ $record->duration }}    
                </x-table.td>

                <x-table.td :responsive="false">
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.aee-records.show', $record)"
                            variant="info"
                        >
                            <i class="fas fa-eye"></i> Ver
                        </x-buttons.link-button>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-5">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
                    Nenhum registro de atendimento AEE encontrado.
                </td>
            </tr>
        @endforelse
    </x-table.table>

@endsection
