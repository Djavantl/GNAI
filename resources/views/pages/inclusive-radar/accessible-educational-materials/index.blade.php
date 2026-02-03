@extends('layouts.master')

@section('title', 'Materiais Pedagógicos Acessíveis')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Materiais Pedagógicos Acessíveis (MPA)</h2>
            <p class="text-muted">Gestão de recursos didáticos, livros e jogos adaptados.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.accessible-educational-materials.create')"
            variant="new"
        >
            Novo Material
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

    <x-table.table :headers="['Material / Tipo', 'Natureza', 'Estoque (Disp. / Total)', 'Status', 'Ativo', 'Ações']">
        @forelse($materials as $material)
            <tr>
                <x-table.td>
                    <div class="d-flex align-items-center">
                        @php
                            $firstImage = $material->inspections->flatMap->images->first();
                        @endphp
                        <div class="me-3 border rounded bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; overflow: hidden;">
                            @if($firstImage)
                                <img src="{{ asset('storage/' . $firstImage->path) }}" class="img-fluid" style="object-fit: cover; height: 100%;">
                            @else
                                <i class="fas {{ $material->type?->is_digital ? 'fa-file-download' : 'fa-book' }} text-secondary"></i>
                            @endif
                        </div>
                        <div>
                            <strong>{{ $material->name }}</strong><br>
                            <small class="text-muted text-uppercase">{{ $material->type?->name ?: 'Didático' }}</small>
                        </div>
                    </div>
                </x-table.td>

                <x-table.td class="text-center">
                    @if($material->type?->is_digital)
                        <span class="badge bg-info">Digital</span>
                    @else
                        <span class="badge bg-warning text-dark">Físico</span>
                    @endif
                </x-table.td>

                <x-table.td class="text-center">
                    @if($material->type?->is_digital)
                        <span class="text-primary font-weight-bold">ILIMITADO</span>
                    @else
                        <div>
                            <span class="badge {{ ($material->quantity_available ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $material->quantity_available ?? 0 }}
                            </span>
                            <span class="text-muted">/ {{ $material->quantity ?? 0 }}</span>
                        </div>
                        <small class="text-muted d-block">{{ $material->asset_code ?: 'S/ PATRIMÔNIO' }}</small>
                    @endif
                </x-table.td>

                <x-table.td class="text-center">
                    @php
                        $isUnavailable = !$material->type?->is_digital && (($material->quantity_available ?? 0) <= 0);
                        $statusLabel = $isUnavailable ? 'Esgotado' : ($material->resourceStatus?->name ?? 'Disponível');
                        $statusClass = $isUnavailable ? 'text-danger' : 'text-muted';
                    @endphp
                    <span class="{{ $statusClass }} font-weight-bold text-uppercase" style="font-size: 0.8rem;">
                        {{ $statusLabel }}
                    </span>
                </x-table.td>

                <x-table.td class="text-center">
                    @if($material->is_active)
                        <span class="text-success font-weight-bold">SIM</span>
                    @else
                        <span class="text-danger font-weight-bold">NÃO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        {{-- Botão Editar com Texto --}}
                        <x-buttons.link-button
                            :href="route('inclusive-radar.accessible-educational-materials.edit', $material)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        {{-- Botão Ativar/Desativar com Texto --}}
                        <form action="{{ route('inclusive-radar.accessible-educational-materials.toggle', $material) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button
                                :variant="$material->is_active ? 'secondary' : 'success'"
                            >
                                {{ $material->is_active ? 'Desativar' : 'Ativar' }}
                            </x-buttons.submit-button>
                        </form>

                        {{-- Botão Excluir com Texto --}}
                        <form action="{{ route('inclusive-radar.accessible-educational-materials.destroy', $material) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja remover este material?')"
                            >
                                Excluir
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
