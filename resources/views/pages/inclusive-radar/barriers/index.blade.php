@extends('layouts.master')

@section('title', 'Mapa de Barreiras')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Mapa de Barreiras</h2>
            <p class="text-muted">Contribuições da comunidade para uma instituição mais acessível.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.barriers.create')"
            variant="new"
        >
            Relatar Barreira
        </x-buttons.link-button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Barreira / Categoria', 'Localização', 'Prioridade', 'Status', 'Relator', 'Ações']">
        @forelse($barriers as $barrier)
            <tr>
                <x-table.td>
                    <div class="d-flex align-items-center">
                        @php
                            $firstImage = $barrier->inspections->first()?->images->first();
                        @endphp
                        <div class="me-3 border rounded bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; overflow: hidden;">
                            @if($firstImage)
                                <img src="{{ asset('storage/' . $firstImage->path) }}" class="img-fluid" style="object-fit: cover; height: 100%;">
                            @else
                                <i class="fas fa-image text-secondary"></i>
                            @endif
                        </div>
                        <div>
                            <strong>{{ $barrier->name }}</strong><br>
                            <small class="text-primary text-uppercase fw-bold" style="font-size: 10px;">
                                {{ $barrier->category->name }}
                            </small>
                        </div>
                    </div>
                </x-table.td>

                <x-table.td>
                    <div class="d-flex flex-column">
                        <span>
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            {{ $barrier->location?->name ?? 'Local não definido' }}
                        </span>
                        <small class="text-muted text-uppercase" style="font-size: 10px;">
                            {{ $barrier->institution->short_name ?? $barrier->institution->name }}
                        </small>
                    </div>
                </x-table.td>

                <x-table.td class="text-center">
                    @php
                        $priority = $barrier->priority;
                        $priorityValue = $priority->value ?? $priority;
                        $priorityLabel = is_object($priority) && method_exists($priority, 'label') ? $priority->label() : ucfirst($priorityValue);

                        $priorityClass = match($priorityValue) {
                            'low'      => 'bg-info',
                            'medium'   => 'bg-warning text-dark',
                            'high'     => 'bg-orange', // Se seu CSS tiver orange, senão use warning
                            'critical' => 'bg-danger',
                            default    => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge {{ $priorityClass }} text-uppercase">
                        {{ $priorityLabel }}
                    </span>
                </x-table.td>

                <x-table.td class="text-center text-uppercase">
                    @php
                        $status = $barrier->latestStatus();
                    @endphp
                    <span class="fw-bold text-muted" style="font-size: 0.8rem;">
                        {{ $status ? $status->label() : '—' }}
                    </span>
                </x-table.td>

                <x-table.td class="text-center">
                    <span class="d-block fw-bold text-dark">
                        @if($barrier->is_anonymous)
                            <i class="fas fa-user-secret text-muted me-1"></i> Anônimo
                        @else
                            {{ $barrier->registeredBy->name ?? 'Sistema' }}
                        @endif
                    </span>
                    @if($barrier->affected_person_role)
                        <small class="text-muted text-uppercase italic" style="font-size: 9px;">
                            {{ $barrier->affected_person_role }}
                        </small>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        {{-- Botão Editar --}}
                        <x-buttons.link-button
                            :href="route('inclusive-radar.barriers.edit', $barrier)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        {{-- Botão Ativar/Desativar --}}
                        <form action="{{ route('inclusive-radar.barriers.toggle', $barrier) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button
                                :variant="$barrier->is_active ? 'secondary' : 'success'"
                            >
                                {{ $barrier->is_active ? 'Desativar' : 'Ativar' }}
                            </x-buttons.submit-button>
                        </form>

                        {{-- Botão Excluir --}}
                        <form action="{{ route('inclusive-radar.barriers.destroy', $barrier) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja remover este relato?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Nenhuma barreira identificada até o momento.</td>
            </tr>
        @endforelse
    </x-table.table>

    @if(method_exists($barriers, 'hasPages') && $barriers->hasPages())
        <div class="mt-4">
            {{ $barriers->links() }}
        </div>
    @endif
@endsection
