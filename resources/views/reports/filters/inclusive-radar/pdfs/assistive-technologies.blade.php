{{-- Só exibe o título se $step estiver definido (apenas para PDF) --}}
@if(isset($step))
    <x-pdf.section-title :title="$step . '. Listagem de Tecnologias Assistivas'" />
@endif

<table class="table-list">
    <thead>
    <tr>
        <th style="width: 30px;">ID</th>
        <th style="width: 150px;">Nome</th>
        <th style="width: 100px;">Categoria</th>
        <th style="width: 80px;">Patrimônio</th>
        <th style="width: 60px;" class="text-center">Qtd/Disp</th>
        <th style="width: 80px;" class="text-center">Estado</th>
        <th style="width: 60px;" class="text-center">Status</th>
        <th style="width: 80px;" class="text-center">Treinamento</th>
        <th style="width: 150px;">Deficiências</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td class="text-center">{{ $item->id }}</td>
            <td><strong>{{ $item->name }}</strong></td>
            <td>{{ $item->type?->name ?? '---' }}</td>
            <td>{{ $item->asset_code ?? '---' }}</td>
            <td class="text-center">{{ $item->quantity }} / {{ $item->quantity_available }}</td>
            <td class="text-center">{{ $item->conservation_state?->label() ?? '---' }}</td>
            <td class="text-center">
                <span class="{{ $item->is_active ? 'status-ativo' : 'status-inativo' }}">
                    {{ $item->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </td>
            <td class="text-center">{{ $item->requires_training ? 'Sim' : 'Não' }}</td>
            <td>{{ $item->deficiencies->pluck('name')->join(', ') ?: '---' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div style="margin-top: 10px; text-align: right; font-size: 10px;">
    <strong>Total de TA:</strong> {{ count($items) }}
</div>
