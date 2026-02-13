<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório de Tecnologias</title>
    <style>
        /* Carrega seu CSS base */
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}

        /* Ajustes para garantir que nada saia da página */
        body {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            table-layout: fixed; /* Força a tabela a respeitar a largura da página */
        }

        .report-table th, .report-table td {
            border: 0.5pt solid #ccc;
            padding: 6px;
            font-size: 9pt;
            word-wrap: break-word; /* Força a quebra de texto longo */
        }

        .report-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-transform: uppercase;
        }

        .text-center { text-align: center; }

        .status-badge {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Relatório Geral de Recursos</h2>
    <p><strong>Total de Registros:</strong> {{ count($data['ta'] ?? []) }}</p>
    <p><strong>Data de Emissão:</strong> {{ now()->format('d/m/Y H:i') }}</p>
</div>

<x-pdf.section-title title="1. Tecnologias Assistivas" />

@if(!empty($data['ta']) && count($data['ta']) > 0)
    <table class="report-table">
        <thead>
        <tr>
            <th style="width: 30px;">ID</th>
            <th style="width: 180px;">Nome / Recurso</th>
            <th style="width: 100px;">Categoria</th>
            <th style="width: 80px;">Patrimônio</th>
            <th style="width: 50px;">Qtd/Disp</th>
            <th style="width: 70px;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data['ta'] as $item)
            <tr>
                <td class="text-center">{{ $item->id }}</td>
                <td>
                    <strong>{{ $item->name }}</strong><br>
                    <small style="color: #666;">{{ $item->conservation_state?->label() ?? '---' }}</small>
                </td>
                <td>{{ $item->type?->name ?? '---' }}</td>
                <td>{{ $item->asset_code ?? '---' }}</td>
                <td class="text-center">{{ $item->quantity }} / {{ $item->quantity_available }}</td>
                <td class="text-center">
                        <span class="status-badge" style="color: {{ $item->is_active ? '#28a745' : '#dc3545' }}">
                            {{ $item->is_active ? 'ATIVO' : 'INATIVO' }}
                        </span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <p style="padding: 20px; text-align: center; border: 1px solid #eee;">
        Nenhum recurso de Tecnologia Assistiva encontrado.
    </p>
@endif

<x-pdf.pages />

</body>
</html>
