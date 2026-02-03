@extends('layouts.master')

@section('title', 'Tecnologias Assistivas')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Tecnologias Assistivas</h2>
            <p class="text-muted">Gerenciamento de periféricos, softwares e equipamentos de acessibilidade.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.assistive-technologies.create')"
            variant="new"
        >
            Nova Tecnologia
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

    <x-table.table :headers="['Equipamento / Tipo', 'Natureza', 'Estoque (Disp. / Total)', 'Status', 'Ativo', 'Ações']">
        @forelse($assistiveTechnologies as $tech)
            <tr>
                <x-table.td>
                    <strong>{{ $tech->name }}</strong><br>
                    <small class="text-muted text-uppercase">{{ $tech->type?->name ?: 'Geral' }}</small>
                </x-table.td>

                <x-table.td class="text-center">
                    @if($tech->type?->is_digital)
                        <span class="badge bg-info">Digital</span>
                    @else
                        <span class="badge bg-warning text-dark">Físico</span>
                    @endif
                </x-table.td>

                <x-table.td class="text-center">
                    @if($tech->type?->is_digital)
                        <span class="text-primary font-weight-bold">ILIMITADO</span>
                    @else
                        <div>
                            <span class="badge {{ $tech->quantity_available > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $tech->quantity_available ?? 0 }}
                            </span>
                            <span class="text-muted">/ {{ $tech->quantity ?? 0 }}</span>
                        </div>
                        <small class="text-muted d-block">{{ $tech->asset_code ?: 'S/ PATRIMÔNIO' }}</small>
                    @endif
                </x-table.td>

                <x-table.td class="text-center">
                    @php
                        $isUnavailable = !$tech->type?->is_digital && ($tech->quantity_available <= 0);
                        $statusLabel = $isUnavailable ? 'Esgotado' : ($tech->resourceStatus?->name ?? 'Disponível');
                        $statusClass = $isUnavailable ? 'text-danger' : 'text-muted';
                    @endphp
                    <span class="{{ $statusClass }} font-weight-bold text-uppercase" style="font-size: 0.8rem;">
                        {{ $statusLabel }}
                    </span>
                </x-table.td>

                <x-table.td class="text-center">
                    @if($tech->is_active)
                        <span class="text-success font-weight-bold">SIM</span>
                    @else
                        <span class="text-danger font-weight-bold">NÃO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.assistive-technologies.edit', $tech)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.assistive-technologies.toggle', $tech) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button
                                :variant="$tech->is_active ? 'secondary' : 'success'"
                            >
                                {{ $tech->is_active ? 'Desativar' : 'Ativar' }}
                            </x-buttons.submit-button>
                        </form>

                        <form action="{{ route('inclusive-radar.assistive-technologies.destroy', $tech) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja remover esta tecnologia?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Nenhuma tecnologia cadastrada.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
