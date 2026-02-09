@extends('layouts.master')

@section('title', 'Minhas Pendências')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Minhas Pendências</h2>
            <p class="text-muted">
                Pendências atribuídas a você como responsável.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Título','Prioridade','Vencimento','Status','Ações']">
        @forelse($pendencies as $pendency)
            <tr>
                <x-table.td>
                    <strong>{{ $pendency->title }}</strong>
                </x-table.td>

                <x-table.td>
                    @php
                        $priorityMap = [
                            'urgent' => ['Urgente', 'danger'],
                            'high'   => ['Alta', 'warning'],
                            'medium' => ['Média', 'secondary'],
                            'low'    => ['Baixa', 'muted'],
                        ];
                        [$priorityLabel, $priorityColor] =
                            $priorityMap[$pendency->priority] ?? [ucfirst($pendency->priority), 'secondary'];
                    @endphp

                    <span class="text-{{ $priorityColor }} fw-bold">
                        {{ $priorityLabel }}
                    </span>
                </x-table.td>

                <x-table.td>
                    {{ $pendency->due_date
                        ? \Carbon\Carbon::parse($pendency->due_date)->format('d/m/Y')
                        : '—'
                    }}
                </x-table.td>

                <x-table.td>
                    @if($pendency->is_completed)
                        <span class="text-success fw-bold">
                            <i class="fas fa-check-circle me-1"></i> Concluída
                        </span>
                    @else
                        <span class="text-danger fw-bold">
                            <i class="fas fa-clock me-1"></i> Pendente
                        </span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.pendencies.show', $pendency)"
                            variant="info"
                        >
                            Ver
                        </x-buttons.link-button>

                        @if(! $pendency->is_completed)
                            <form
                                action="{{ route('specialized-educational-support.pendencies.complete', $pendency) }}"
                                method="POST"
                                onsubmit="return confirm('Marcar esta pendência como concluída?')"
                            >
                                @csrf
                                @method('PUT')

                                <x-buttons.submit-button variant="success">
                                    Concluir
                                </x-buttons.submit-button>
                            </form>
                        @endif
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="fas fa-check-circle me-1"></i>
                    Nenhuma pendência atribuída a você no momento.
                </td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
