@extends('layouts.master')

@section('title', 'Status do Sistema')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Status dos Recursos' => route('inclusive-radar.resource-statuses.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Status dos Recursos</h2>
            <p class="text-muted">Gerencie como os recursos são classificados e as regras de empréstimo.</p>
        </div>
    </div>

    <x-table.table :headers="['Nome do Status', 'Aplicabilidade', 'Regra de Empréstimo', 'Status', 'Ações']">
        @foreach($resourceStatuses as $resourceStatus)
            <tr>
                {{-- NOME --}}
                <x-table.td>
                    {{ $resourceStatus->name ?? 'N/A' }}
                </x-table.td>

                {{-- APLICABILIDADE --}}
                <x-table.td>
                    @php
                        $apps = [];
                        if($resourceStatus->for_assistive_technology) $apps[] = 'Tecnologia';
                        if($resourceStatus->for_educational_material) $apps[] = 'Material';
                    @endphp
                    {{ count($apps) > 0 ? implode(' / ', $apps) : 'N/A' }}
                </x-table.td>

                {{-- REGRA DE EMPRÉSTIMO --}}
                <x-table.td>
                    @if($resourceStatus->blocks_loan)
                        <span class="text-danger fw-bold">Bloqueia Empréstimo</span>
                    @else
                        <span class="text-success fw-bold">Liberado para Uso</span>
                    @endif
                </x-table.td>

                {{-- ATIVO/INATIVO --}}
                <x-table.td>
                    @php
                        $statusColor = $resourceStatus->is_active ? 'success' : 'danger';
                        $statusLabel = $resourceStatus->is_active ? 'Ativo' : 'Inativo';
                    @endphp
                    <span class="text-{{ $statusColor }} fw-bold">
                        {{ $statusLabel }}
                    </span>
                </x-table.td>

                {{-- AÇÕES --}}
                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.resource-statuses.show', $resourceStatus)"
                            variant="info"
                        >
                            Ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('inclusive-radar.resource-statuses.edit', $resourceStatus)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @endforeach
    </x-table.table>
@endsection
