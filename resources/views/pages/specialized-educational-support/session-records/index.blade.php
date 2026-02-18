@extends('layouts.master')

@section('title', 'Registros de Sessão')

@section('content')
     <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Registros de Sessões' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Registros de Sessão</h2>
            <p class="text-muted">Acompanhamento dos atendimentos educacionais especializados.</p>
        </div>
        <div class="d-flex gap-2 align-items-start">
            {{-- Se houver um ID de sessão no filtro, permite criar novo registro para AQUELA sessão --}}
            @if(request()->has('session_id'))
                <x-buttons.link-button
                    :href="route('specialized-educational-support.sessions.index')"
                    variant="secondary"
                >
                    Voltar para Sessões
                </x-buttons.link-button>

                {{-- Corrigido: Rota agora espera o parâmetro {session} conforme definido nas rotas --}}
                <x-buttons.link-button
                    :href="route('specialized-educational-support.session-records.create', request('session_id'))"
                    variant="new"
                >
                    Novo Registro
                </x-buttons.link-button>
            @endif
        </div>
    </div>


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Data / Horário', 'Alunos na Sessão', 'Duração', 'Status/Obs', 'Ações']">
        @forelse($sessionRecords as $record)
            <tr>
                <x-table.td>
                    {{-- Corrigido: Pegando a data da sessão vinculada --}}
                    <strong>{{ $record->attendanceSession->session_date->format('d/m/Y') }}</strong><br>
                    <small class="text-muted">{{ $record->attendanceSession->start_time }}</small>
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
                        <small class="text-muted">Sessão #{{ $record->attendance_session_id }}</small>
                    @else
                        <span class="text-warning small">Nenhum aluno avaliado</span>
                    @endif
                </x-table.td>

                <x-table.td class="text-center">
                    <span class="badge bg-info text-dark" style="font-size: 0.8rem;">
                        <i class="far fa-clock mr-1"></i> {{ $record->duration }}
                    </span>
                </x-table.td>

                <x-table.td>
                    <div class="small text-muted">
                        {{-- Mostra observação geral ou resumo --}}
                        <strong>Obs:</strong> {{ Str::limit($record->general_observations ?? 'Sem observações gerais.', 50) }}
                    </div>
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.session-records.show', $record)"
                            variant="primary"
                        >
                            Ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.session-records.edit', $record)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('specialized-educational-support.session-records.destroy', $record) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button variant="danger" onclick="return confirm('Excluir este registro e todas as avaliações individuais vinculadas?')">
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-notes-medical d-block mb-2" style="font-size: 2.5rem; color: #dee2e6;"></i>
                    Nenhum registro de sessão encontrado.
                </td>
            </tr>
        @endforelse
    </x-table.table>

    <div class="mt-4 d-flex justify-content-between align-items-center">
        <x-buttons.link-button
            :href="route('specialized-educational-support.sessions.index')"
            variant="secondary"
        >
            <i class="fas fa-chevron-left mr-1"></i> Voltar para Sessões
        </x-buttons.link-button>
        
        <span class="text-muted small">Total: {{ $sessionRecords->count() }} registro(s)</span>
    </div>
@endsection