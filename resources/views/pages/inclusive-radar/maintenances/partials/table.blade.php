<x-table.table :headers="['Recurso', 'Status', 'Etapa 1', 'Etapa 2', 'Ações']" :records="$resources">
    @php
        // Criamos uma coleção plana de todas as manutenções de todos os recursos retornados
        $allMaintenances = collect();
        foreach($resources as $resource) {
            foreach($resource->maintenances as $m) {
                // Injetamos o recurso dentro da manutenção para facilitar o acesso no loop
                $m->resource_context = $resource;
                $allMaintenances->push($m);
            }
        }

        // Aplicamos o filtro de status na coleção, se houver
        $statusParam = request('status');
        if ($statusParam === 'completed') {
            $allMaintenances = $allMaintenances->where('status', \App\Enums\InclusiveRadar\MaintenanceStatus::COMPLETED);
        } elseif ($statusParam === 'pending') {
            $allMaintenances = $allMaintenances->where('status', '!=', \App\Enums\InclusiveRadar\MaintenanceStatus::COMPLETED);
        }

        // Ordena por ID decrescente para as mais novas ficarem em cima
        $allMaintenances = $allMaintenances->sortByDesc('id');
    @endphp

    @forelse($allMaintenances as $maintenance)
        @php
            $resource = $maintenance->resource_context;
            $stage1 = $maintenance->stages->firstWhere('step_number', 1);
            $stage2 = $maintenance->stages->firstWhere('step_number', 2);
            $resourceType = ($resource instanceof \App\Models\InclusiveRadar\AssistiveTechnology) ? 'ta' : 'mpa';
        @endphp

        <tr>
            {{-- RECURSO --}}
            <x-table.td class="fw-bold">
                {{ $resource->name }}
                <br><small class="text-muted fw-normal">ID Manutenção: #{{ $maintenance->id }}</small>
            </x-table.td>

            {{-- STATUS --}}
            <x-table.td>
                @if($maintenance->status === \App\Enums\InclusiveRadar\MaintenanceStatus::PENDING)
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning">Pendente</span>
                @else
                    <span class="badge bg-success-subtle text-success-emphasis border border-success">Concluída</span>
                @endif
            </x-table.td>

            {{-- ETAPA 1 --}}
            <x-table.td>
                @if($stage1)
                    <div class="small">
                        <span class="text-muted">Início:</span> {{ $stage1->starter?->name ?? '—' }}<br>
                        @if($stage1->completed_at)
                            <span class="text-success"><i class="fas fa-check"></i> Finalizada</span>
                        @else
                            <span class="badge bg-light text-dark border italic">Em andamento</span>
                        @endif
                    </div>
                @else
                    <span class="text-muted small">Aguardando</span>
                @endif
            </x-table.td>

            {{-- ETAPA 2 --}}
            <x-table.td>
                @if($stage2)
                    <div class="small">
                        <span class="text-muted">Técnico:</span> {{ $stage2->starter?->name ?? '—' }}<br>
                        @if($stage2->completed_at)
                            <span class="text-success"><i class="fas fa-check"></i> Finalizada</span>
                        @else
                            <span class="badge bg-light text-dark border italic">Em andamento</span>
                        @endif
                    </div>
                @else
                    <span class="text-muted small">Não iniciada</span>
                @endif
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>

                    @if($maintenance->status === \App\Enums\InclusiveRadar\MaintenanceStatus::COMPLETED || ($stage1 && $stage1->completed_at))
                        <x-buttons.link-button
                            :href="route('inclusive-radar.maintenances.show', $maintenance)"
                            variant="info"
                            class="btn-sm"
                        >
                            <i class="fas fa-eye"></i> Ver
                        </x-buttons.link-button>
                    @endif

                    {{-- AÇÕES DE EDIÇÃO (ETAPAS) --}}
                    @if($maintenance->status !== \App\Enums\InclusiveRadar\MaintenanceStatus::COMPLETED)

                        {{-- Etapa 1: Aparece se não estiver concluída --}}
                        @if(!$stage1?->completed_at)
                            <x-buttons.link-button
                                :href="route('inclusive-radar.maintenances.stage1', $maintenance)"
                                variant="primary"
                                class="btn-sm"
                            >
                                <i class="fas fa-tools me-1"></i> Etapa 1
                            </x-buttons.link-button>
                        @endif

                        {{-- Etapa 2: Só aparece se a Etapa 1 foi concluída e a 2 ainda não --}}
                        @if($stage1?->completed_at && !$stage2?->completed_at)
                            <x-buttons.link-button
                                :href="route('inclusive-radar.maintenances.stage2', $maintenance)"
                                variant="primary"
                                class="btn-sm"
                            >
                                <i class="fas fa-clipboard-check me-1"></i> Etapa 2
                            </x-buttons.link-button>
                        @endif
                    @endif
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-5">
                <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                Nenhuma manutenção encontrada.
            </td>
        </tr>
    @endforelse
</x-table.table>
