<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório Geral</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
        .table-list { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        .table-list th, .table-list td { border: 1px solid #ccc; padding: 6px; font-size: 9px; word-wrap: break-word; vertical-align: middle; }
        .table-list th { background-color: #f2f2f2; font-weight: bold; text-align: left; }
        .text-center { text-align: center; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

@php $step = 1; @endphp

@foreach($data as $module => $moduleItems)
    @if(!empty($moduleItems) && count($moduleItems) > 0)
        @php
            $viewName = match($module) {
                'ta' => 'assistive-technologies',
                'materials' => 'accessible-educational-materials',
                default => $module
            };
            $items = $moduleItems;
            $moduleFilterText = implode(' | ', $moduleFilters[$module] ?? []);
        @endphp

        <div class="header">
            <h2>
                {{ $module === 'ta' ? 'Relatório de Tecnologias Assistivas' : 'Relatório de Materiais Pedagógicos Acessíveis' }}
            </h2>
            <p style="font-size: 10px; color: #666;">
                <strong>Filtros:</strong> {{ $moduleFilterText ?: 'Sem filtros' }}
            </p>
            <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        {{-- Inclui a tabela específica do módulo --}}
        @includeFirst([
            "reports.filters.inclusive-radar.pdfs.{$viewName}",
            "reports.filters.inclusive-radar.pdfs.default"
        ], [
            'items' => $items,
            'step' => $step++,
            'filterText' => $moduleFilterText
        ])

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endif
@endforeach

<x-pdf.pages />
</body>
</html>
