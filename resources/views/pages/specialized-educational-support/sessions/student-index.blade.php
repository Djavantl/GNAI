@extends('layouts.master')

@section('title', "Sessões de Atendimento - {$student->person->name}")

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Prontuários' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Sessões' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="text-title">Sessões de Atendimento</h2>
            <p class="text-muted">Histórico de atendimentos para: <strong>{{ $student->person->name }}</strong></p>
        </div>
        <div class="d-flex gap-2">

            <x-buttons.link-button
                :href="route('specialized-educational-support.students.show', $student->id)"
                variant="secondary"
            >
                Voltar ao Prontuário
            </x-buttons.link-button>
        </div>
    </div>

    <x-table.table :headers="['Data', 'Profissional', 'Tipo', 'Status', 'Ações']">
    @forelse($sessions as $session)
        <tr>
            <x-table.td>
                <div class="fw-bold">{{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</div>
                <small class="text-muted">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}</small>
            </x-table.td>
            
            <x-table.td>{{ $session->professional->person->name }}</x-table.td>
            
            <x-table.td>
                <span class="badge bg-light text-dark border">
                    {{ $session->type === 'group' ? 'Grupo' : 'Individual' }}
                </span>
            </x-table.td>

            <x-table.td>
                @php
                    $statusValue = strtolower($session->status);
                    $statusColor = match($statusValue) {
                        'scheduled', 'agendado' => 'info',
                        'completed', 'realizado' => 'success',
                        'canceled', 'cancelled', 'cancelado' => 'danger',
                        default => 'warning'
                    };
                @endphp
                <span class="text-{{ $statusColor }} fw-bold">
                    {{ ucfirst($session->status) }}
                </span>
            </x-table.td>

            <x-table.td>
                <x-table.actions>
                    {{-- Ver --}}
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.sessions.show', $session->id)"
                        variant="info"
                    >
                        Ver
                    </x-buttons.link-button>

                    {{-- Editar (apenas se não estiver cancelada) --}}
                    @if(!in_array(strtolower($session->status), ['canceled', 'cancelled', 'cancelado']))
                        <x-buttons.link-button 
                            :href="route('specialized-educational-support.sessions.edit', $session->id)" 
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>
                    @endif

                    {{-- Registro --}}
                    @if($session->sessionRecord)
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.session-records.show', $session->sessionRecord->id)"
                            variant="dark"
                        >
                            Ver Registro
                        </x-buttons.link-button>
                    @else
                        @if(!in_array(strtolower($session->status), ['canceled', 'cancelled', 'cancelado']))
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.session-records.create', $session->id)"
                                variant="new"
                            >
                                Registrar
                            </x-buttons.link-button>
                        @endif
                    @endif

                    {{-- Excluir --}}
                    <form action="{{ route('specialized-educational-support.sessions.destroy', $session->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Mover esta sessão para a lixeira?')"
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
                    Nenhuma sessão encontrada para este aluno.
                </td>
            </tr>
    @endforelse
    </x-table.table>
@endsection