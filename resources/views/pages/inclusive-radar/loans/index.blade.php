@extends('layouts.master')

@section('title', 'Empréstimos de Recursos')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Empréstimos de Recursos</h2>
            <p class="text-muted">Controle de saídas e devoluções de tecnologias e materiais pedagógicos.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.loans.create')"
            variant="new"
        >
            Novo Empréstimo
        </x-buttons.link-button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <p class="mb-0">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <x-table.table :headers="['Recurso / Item', 'Beneficiário (Estudante)', 'Data Saída', 'Prazo Entrega', 'Status', 'Ações']">
        @forelse($loans as $loan)
            <tr>
                <x-table.td>
                    <strong>{{ $loan->loanable->name ?? ($loan->loanable->title ?? ($loan->loanable->description ?? 'Item Removido')) }}</strong><br>
                    <small class="text-muted text-uppercase">
                        {{ $loan->loanable_type === 'App\Models\InclusiveRadar\AssistiveTechnology' ? 'Tecnologia' : 'Material' }}
                    </small>
                </x-table.td>

                <x-table.td>
                    <strong>{{ $loan->student->person->name }}</strong><br>
                    <small class="text-muted italic">Matrícula: {{ $loan->student->registration }}</small>
                </x-table.td>

                <x-table.td class="text-center text-muted">
                    {{ $loan->loan_date->format('d/m/Y H:i') }}
                </x-table.td>

                <x-table.td class="text-center">
                    <span class="{{ $loan->status === 'active' && $loan->due_date->isPast() ? 'text-danger font-weight-bold' : 'text-muted' }}">
                        {{ $loan->due_date->format('d/m/Y') }}
                    </span>
                </x-table.td>

                <x-table.td class="text-center">
                    @if($loan->status === 'active')
                        @if($loan->due_date->isPast())
                            <span class="badge bg-danger">EM ATRASO</span>
                        @else
                            <span class="badge bg-success">ATIVO</span>
                        @endif
                    @elseif($loan->status === 'late')
                        <span class="badge bg-warning text-dark">DEVOLVIDO (ATRASO)</span>
                    @elseif($loan->status === 'returned')
                        <span class="badge bg-secondary">DEVOLVIDO</span>
                    @elseif($loan->status === 'damaged')
                        <span class="badge bg-dark">COM AVARIA</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        @if($loan->status === 'active')
                            <form action="{{ route('inclusive-radar.loans.return', $loan) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <x-buttons.submit-button
                                    variant="success"
                                    onclick="return confirm('Confirmar a devolução?')"
                                >
                                    Devolver
                                </x-buttons.submit-button>
                            </form>
                        @endif

                        <x-buttons.link-button
                            :href="route('inclusive-radar.loans.edit', $loan)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.loans.destroy', $loan) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja excluir este registro?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Nenhum empréstimo registrado.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
