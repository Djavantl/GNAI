<x-pdf.section-title title="{{ $step }}. Listagem de Materiais Pedagógicos" />

<table class="table-list">
    <thead>
    <tr>
        <th style="width: 30px;">ID</th>
        <th style="width: 120px;">Nome</th>
        <th style="width: 100px;">Categoria</th>
        <th style="width: 60px;" class="text-center">Qtd/Disp</th>
        <th style="width: 100px;">Requer Treinamento</th>
        <th style="width: 150px;">Deficiências Atendidas</th>
        <th style="width: 150px;">Recursos de Acessibilidade</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td class="text-center">{{ $item->id }}</td>
            <td><strong>{{ $item->name }}</strong></td>
            <td>{{ $item->type?->name ?? '---' }}</td>
            <td class="text-center">{{ $item->quantity }} / {{ $item->quantity_available }}</td>
            <td class="text-center">
                {{ $item->requires_training ? 'Sim' : 'Não' }}
            </td>
            <td>
                {{ $item->deficiencies->pluck('name')->join(', ') ?: '---' }}
            </td>
            <td>
                {{ $item->accessibilityFeatures->pluck('name')->join(', ') ?: '---' }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div style="margin-top: 10px; text-align: right; font-size: 10px;">
    <strong>Total de Materiais:</strong> {{ count($items) }}
</div>
