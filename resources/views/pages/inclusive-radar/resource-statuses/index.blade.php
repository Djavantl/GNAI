@extends('layouts.master')

@section('title', 'Status do Sistema')

@section('content')
    <x-messages.toast />

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Status do Sistema</h2>
            <p class="text-muted">Gerencie como os recursos são classificados e as regras de empréstimo.</p>
        </div>
    </div>

    <x-table.table :headers="['Nome do Status', 'Aplicabilidade', 'Regra de Empréstimo', 'Status', 'Ações']">
        @foreach($statuses as $status)
            <tr>
                {{-- NOME: Texto direto, descrição em small --}}
                <x-table.td>
                    {{ $status->name ?? 'N/A' }}
                </x-table.td>

                {{-- APLICABILIDADE: Texto simples separado por barra --}}
                <x-table.td>
                    @php
                        $apps = [];
                        if($status->for_assistive_technology) $apps[] = 'Tecnologia';
                        if($status->for_educational_material) $apps[] = 'Material';
                    @endphp
                    {{ count($apps) > 0 ? implode(' / ', $apps) : 'N/A' }}
                </x-table.td>

                {{-- REGRA DE EMPRÉSTIMO: Apenas texto colorido/bold --}}
                <x-table.td>
                    @if($status->blocks_loan)
                        <span class="text-danger fw-bold">Bloqueia Empréstimo</span>
                    @else
                        <span class="text-success fw-bold">Liberado para Uso</span>
                    @endif
                </x-table.td>

                {{-- ATIVO/INATIVO: Padronizado com Students --}}
                <x-table.td>
                    @php
                        $statusColor = $status->is_active ? 'success' : 'danger';
                        $statusLabel = $status->is_active ? 'Ativo' : 'Inativo';
                    @endphp
                    <span class="text-{{ $statusColor }} fw-bold">
                        {{ $statusLabel }}
                    </span>
                </x-table.td>

                {{-- AÇÕES: Apenas Editar --}}
                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.resource-statuses.edit', $status)"
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
