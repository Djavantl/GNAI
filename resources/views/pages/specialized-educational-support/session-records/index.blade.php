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
            @if(request()->has('session_id'))
                <x-buttons.link-button
                    :href="route('specialized-educational-support.sessions.index')"
                    variant="secondary"
                >
                    Voltar para Sessões
                </x-buttons.link-button>

                <x-buttons.link-button
                    :href="route('specialized-educational-support.session-records.create', ['session_id' => request('session_id')])"
                    variant="new"
                >
                    Novo Registro
                </x-buttons.link-button>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('specialized-educational-support.session-records.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Filtrar por Aluno</label>
                    <select name="student_id" class="form-select">
                        <option value="">Todos os alunos</option>
                        {{-- @foreach($students as $student) ... @endforeach --}}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Data Início</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Data Fim</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <x-buttons.submit-button variant="primary" class="w-100">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </x-buttons.submit-button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Data / Horário', 'Aluno / Sessão', 'Duração', 'Avaliação', 'Ações']">
        @forelse($sessionRecords as $record)
            <tr>
                <x-table.td>
                    <strong>{{ $record->record_date ? \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') : 'N/A' }}</strong><br>
                    <small class="text-muted">{{ $record->created_at->format('H:i') }}</small>
                </x-table.td>

                <x-table.td>
                    @if($record->session && $record->session->student)
                        <strong>{{ $record->session->student->person->name }}</strong><br>
                        <small class="text-muted">Sessão #{{ $record->attendance_sessions_id }}</small>
                    @else
                        <span class="text-danger">Sessão não vinculada</span>
                    @endif
                </x-table.td>

                <x-table.td class="text-center">
                    <span class="badge bg-info text-dark" style="font-size: 0.8rem;">
                        <i class="far fa-clock mr-1"></i> {{ $record->duration }}
                    </span>
                </x-table.td>

                <x-table.td>
                    <div class="small text-muted" title="{{ $record->development_evaluation }}">
                        {{ Str::limit($record->development_evaluation, 60) }}
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
                            <x-buttons.submit-button variant="danger" onclick="return confirm('Excluir este registro de sessão?')">
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