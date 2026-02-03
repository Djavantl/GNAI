@extends('layouts.master')

@section('title', 'Instituições Base')

@section('content')
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

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Instituição', 'Localização', 'Coordenadas', 'Status', 'Ações']">
        @forelse($institutions as $inst)
            <tr>
                <x-table.td>
                    <strong>{{ $inst->name }}</strong><br>
                    <small class="text-muted text-uppercase">{{ $inst->short_name ?? 'Sem sigla' }}</small>
                </x-table.td>

                <x-table.td>
                    {{ $inst->city }} - {{ $inst->state }}
                </x-table.td>

                <x-table.td>
                    <code class="text-primary" style="font-size: 0.8rem;">
                        {{ number_format($inst->latitude, 5) }}, {{ number_format($inst->longitude, 5) }}
                    </code>
                </x-table.td>

                <x-table.td class="text-center">
                    @php
                        $statusColor = $inst->is_active ? 'success' : 'danger';
                        $statusLabel = $inst->is_active ? 'Ativo' : 'Inativo';
                    @endphp
                    <span class="badge bg-{{ $statusColor }} text-uppercase">
                        {{ $statusLabel }}
                    </span>
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        {{-- Botão Editar --}}
                        <x-buttons.link-button
                            :href="route('inclusive-radar.institutions.edit', $inst)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        {{-- Botão Alternar Status (Ativar/Desativar) --}}
                        <form action="{{ route('inclusive-radar.institutions.toggle-active', $inst) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button
                                :variant="$inst->is_active ? 'secondary' : 'success'"
                            >
                                {{ $inst->is_active ? 'Desativar' : 'Ativar' }}
                            </x-buttons.submit-button>
                        </form>

                        {{-- Botão Excluir --}}
                        <form action="{{ route('inclusive-radar.institutions.destroy', $inst) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Tem certeza que deseja excluir esta instituição? Todas as localizações vinculadas serão removidas.')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">Nenhuma instituição cadastrada até o momento.</td>
            </tr>
        @endforelse
    </x-table.table>

    <div class="mt-3 text-muted" style="font-size: 0.85rem;">
        <i class="fas fa-info-circle me-1"></i>
        <span>O status "Ativo" define qual instituição será usada como base padrão para novos relatos.</span>
    </div>
@endsection
