@extends('layouts.master')

@section('title', 'Sessões de Atendimento')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-title">Sessões de Atendimento</h2>
        <div class="d-flex gap-2">
            <x-buttons.link-button
                :href="route('specialized-educational-support.session-records.index')"
                variant="dark"
            >
                Registros
            </x-buttons.link-button>

            <x-buttons.link-button
                :href="route('specialized-educational-support.sessions.create')"
                variant="new"
            >
                Nova Sessão
            </x-buttons.link-button>
        </div>
    </div>

    <x-table.table :headers="['Data', 'Aluno', 'Profissional', 'Tipo', 'Status', 'Ações']">
    @forelse(collect($sessions) as $session)
        <tr>
            <x-table.td>{{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</x-table.td>
            <x-table.td>
                @forelse($session->students ?? [] as $student)
                    <div>{{ $student->person->name }}</div>
                @empty
                    <span class="text-muted">Sem alunos</span>
                @endforelse
            </x-table.td>
            <x-table.td>{{ $session->professional->person->name }}</x-table.td>
            <x-table.td>{{ $session->type }}</x-table.td>
            <x-table.td>
                @php
                    // Mapeando cores para os diferentes status de sessão
                    $statusColor = match($session->status) {
                        'scheduled' => 'info',
                        'completed' => 'success',
                        'canceled'  => 'danger',
                        default     => 'warning'
                    };
                    $statusLabel = ucfirst($session->status);
                @endphp

                <span class="text-{{ $statusColor }} fw-bold">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td>
                <x-table.actions>
                    {{-- Ver Sessão --}}
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.sessions.show', $session)"
                        variant="info"
                    >
                        Ver
                    </x-buttons.link-button>

                    {{-- Editar Sessão --}}
                    @if($session->status !== 'cancelled' && $session->status !== 'Cancelado')
                        <x-buttons.link-button :href="route('specialized-educational-support.sessions.edit', $session->id)" variant="warning">
                            Editar Sessão
                        </x-buttons.link-button>
                    @endif

                    {{-- Lógica do Registro --}}
                    @if($session->sessionRecord)
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.session-records.show', $session->sessionRecord->id)"
                            variant="dark"
                        >
                            Ver Registro
                        </x-buttons.link-button>
                    @else
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.session-records.create', $session->id)"
                            variant="new"
                        >
                            Criar Registro
                        </x-buttons.link-button>
                    @endif

                    {{-- Excluir --}}
                    <form action="{{ route('specialized-educational-support.sessions.destroy', $session) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Mover para lixeira?')"
                        >
                            Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-5">
                    Nenhuma sessão cadastrada.
                </td>
            </tr>
    @endforelse
    </x-table.table>
@endsection
