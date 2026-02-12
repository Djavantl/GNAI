@extends('layouts.master')

@section('title', 'Pendências')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Pendências' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-title mb-0">Pendências</h2>

        <div class="d-flex gap-2">
            <x-buttons.link-button 
                :href="route('specialized-educational-support.pendencies.create')" 
                variant="new">
                Nova Pendência
            </x-buttons.link-button>

            <x-buttons.link-button
                :href="route('specialized-educational-support.pendencies.my')"
                variant="info"
            >
                <i class="fas fa-user-check me-1"></i> Minhas Pendências
            </x-buttons.link-button>
        </div>
    </div>

    <x-table.table :headers="['Título','Profissional','Prioridade','Vencimento','Concluída','Ações']">
        @foreach($pendencies as $pendency)
            <tr>
                <x-table.td>{{ $pendency->title }}</x-table.td>

                <x-table.td>
                    {{-- tenta exibir nome do profissional, fallback para id --}}
                    {{ optional(\App\Models\SpecializedEducationalSupport\Professional::find($pendency->assigned_to))->person->name ?? ('#' . $pendency->assigned_to) }}
                </x-table.td>

                <x-table.td>
                   <span class="text-{{ $pendency->priority->color() }} fw-bold">
                        {{ $pendency->priority->label() }}
                    </span>
                </x-table.td>

                <x-table.td>
                    {{ $pendency->due_date ? \Carbon\Carbon::parse($pendency->due_date)->format('d/m/Y') : '—' }}
                </x-table.td>

                <x-table.td>
                    @if($pendency->is_completed)
                        <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>Sim</span>
                    @else
                        <span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i>Não</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button :href="route('specialized-educational-support.pendencies.show', $pendency)" variant="info">
                            Ver
                        </x-buttons.link-button>

                        <x-buttons.link-button :href="route('specialized-educational-support.pendencies.edit', $pendency)" variant="warning">
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('specialized-educational-support.pendencies.destroy', $pendency) }}" method="POST" onsubmit="return confirm('Deseja excluir esta pendência?')">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button variant="danger">Excluir</x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @endforeach
    </x-table.table>
@endsection
