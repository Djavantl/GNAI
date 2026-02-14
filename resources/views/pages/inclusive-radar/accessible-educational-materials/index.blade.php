@extends('layouts.master')

@section('title', 'Materiais Pedagógicos Acessíveis')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Materiais Pedagógicos Acessíveis' => route('inclusive-radar.accessible-educational-materials.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Materiais Pedagógicos Acessíveis</h2>
            <p class="text-muted text-base">Gestão de recursos didáticos, livros e jogos adaptados.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.accessible-educational-materials.create')"
            variant="new"
        >
            Novo Material
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Nome', 'Tipo', 'Natureza', 'Estoque', 'Status', 'Ações']">
        @forelse($materials as $material)
            <tr>
                {{-- NOME: Direto na TD como em Alunos --}}
                <x-table.td>{{ $material->name }}</x-table.td>

                {{-- TIPO --}}
                <x-table.td>{{ $material->type?->name ?: 'Didático' }}</x-table.td>

                {{-- NATUREZA --}}
                <x-table.td>{{ $material->type?->is_digital ? 'Digital' : 'Físico' }}</x-table.td>

                {{-- ESTOQUE: Mantida apenas a lógica de cor --}}
                <x-table.td>
                    @if($material->type?->is_digital)
                        <span class="text-info fw-bold">Ilimitado</span>
                    @else
                        <span class="{{ ($material->quantity_available ?? 0) > 0 ? 'text-success' : 'text-danger' }} fw-medium">
                            {{ $material->quantity_available ?? 0 }}
                        </span>
                        <span class="text-muted">/ {{ $material->quantity ?? 0 }}</span>
                    @endif
                </x-table.td>

                {{-- STATUS: Padrão Alunos --}}
                <x-table.td>
                    @php
                        $isUnavailable = !$material->type?->is_digital && (($material->quantity_available ?? 0) <= 0);
                        $stColor = $isUnavailable ? 'danger' : ($material->is_active ? 'success' : 'secondary');
                        $stLabel = $isUnavailable ? 'Esgotado' : ($material->is_active ? 'Ativo' : 'Inativo');
                    @endphp
                    <span class="text-{{ $stColor }} fw-bold">
                        {{ $stLabel }}
                    </span>
                </x-table.td>

                {{-- AÇÕES: Simpificado como em Alunos --}}
                {{-- AÇÕES: Simpificado como em Alunos --}}
                <x-table.td>
                    <x-table.actions>
                        {{-- Botão para ver MPA --}}
                        <x-buttons.link-button
                            :href="route('inclusive-radar.accessible-educational-materials.show', $material)"
                            variant="info"
                        >
                            <i class="fas fa-eye me-1"></i> Ver
                        </x-buttons.link-button>

                        {{-- Botão para editar MPA --}}
                        <x-buttons.link-button
                            :href="route('inclusive-radar.accessible-educational-materials.edit', $material)"
                            variant="warning"
                        >
                            <i class="fas fa-edit me-1"></i> Editar
                        </x-buttons.link-button>

                        {{-- Botão para excluir MPA --}}
                        <form action="{{ route('inclusive-radar.accessible-educational-materials.destroy', $material) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja remover este material?')"
                            >
                                <i class="fas fa-trash-alt me-1"></i> Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Nenhum material pedagógico cadastrado.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
