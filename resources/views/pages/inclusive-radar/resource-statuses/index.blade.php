@extends('layouts.master')

@section('title', 'Status do Sistema')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Status do Sistema</h2>
            <p class="text-muted">Gerencie como os recursos são classificados e quais regras de empréstimo se aplicam a cada estado.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Nome do Status', 'Aplicabilidade', 'Regra de Empréstimo', 'Ativo', 'Ações']">
        @foreach($statuses as $status)
            <tr>
                <x-table.td>
                    <span class="fw-bold text-purple-dark fs-6">{{ $status->name }}</span>
                    @if($status->description)
                        <br><small class="text-muted italic">{{ Str::limit($status->description, 50) }}</small>
                    @endif
                </x-table.td>

                <x-table.td class="text-center">
                    <div class="d-flex flex-column gap-1 align-items-center">
                        @if($status->for_assistive_technology)
                            <span class="badge bg-light text-primary border w-100" style="font-size: 0.7rem;">TECNOLOGIA</span>
                        @endif
                        @if($status->for_educational_material)
                            <span class="badge bg-light text-success border w-100" style="font-size: 0.7rem;">MATERIAL</span>
                        @endif
                    </div>
                </x-table.td>

                <x-table.td class="text-center">
                    @if($status->blocks_loan)
                        <span class="badge shadow-sm border"
                              style="background-color: #fff1f2; color: #be123c; border-color: #fecdd3 !important;">
                            <i class="fas fa-ban me-1"></i> Bloqueia Empréstimo
                        </span>
                    @else
                        <span class="badge shadow-sm border"
                              style="background-color: #f0fdf4; color: #15803d; border-color: #bbf7d0 !important;">
                            <i class="fas fa-check-circle me-1"></i> Liberado para Uso
                        </span>
                    @endif
                </x-table.td>

                <x-table.td class="text-center">
                    @if($status->is_active)
                        <span class="text-success font-weight-bold">SIM</span>
                    @else
                        <span class="text-danger font-weight-bold">NÃO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.resource-statuses.edit', $status)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.resource-statuses.toggle-active', $status) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button
                                :variant="$status->is_active ? 'secondary' : 'success'"
                            >
                                {{ $status->is_active ? 'Desativar' : 'Ativar' }}
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @endforeach
    </x-table.table>

    <style>
        .text-purple-dark { color: #4c1d95; }
    </style>
@endsection
