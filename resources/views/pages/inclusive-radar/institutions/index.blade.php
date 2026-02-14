@extends('layouts.master')

@section('title', 'Instituições')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Instituições' => route('inclusive-radar.institutions.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Instituições Base</h2>
            <p class="text-muted">Gerencie os locais centrais onde o radar de acessibilidade opera.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.institutions.create')"
            variant="new"
        >
            Nova Instituição
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Nome', 'Localização', 'Status', 'Ações']">
        @forelse($institutions as $inst)
            <tr>
                {{-- INSTITUIÇÃO: Texto direto --}}
                <x-table.td>
                    {{ $inst->name }}
                </x-table.td>

                {{-- LOCALIZAÇÃO: Texto direto --}}
                <x-table.td>
                    {{ $inst->city }} - {{ $inst->state }}
                </x-table.td>

                {{-- STATUS: Padrão Students --}}
                <x-table.td>
                    @php
                        $statusColor = $inst->is_active ? 'success' : 'danger';
                        $statusLabel = $inst->is_active ? 'Ativo' : 'Inativo';
                    @endphp
                    <span class="text-{{ $statusColor }} fw-bold">
                        {{ $statusLabel }}
                    </span>
                </x-table.td>

                {{-- AÇÕES: Editar e Excluir --}}
                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.institutions.show', $inst)"
                            variant="info"
                        >
                            <i class="fas fa-eye"></i> Ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('inclusive-radar.institutions.edit', $inst)"
                            variant="warning"
                        >
                            <i class="fas fa-edit"></i> Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.institutions.destroy', $inst) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Tem certeza que deseja excluir esta instituição?')"
                            >
                                <i class="fas fa-trash-alt"></i> Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">Nenhuma instituição cadastrada até o momento.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
