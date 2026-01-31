<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* Configurações de impressão e página */
        @page {
            margin: 2cm;
            size: A4;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt; /* Pontos é melhor que pixels para PDF */
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Títulos com mais peso visual */
        h1 {
            text-align: center;
            text-transform: uppercase;
            font-size: 18pt;
            margin-bottom: 5px;
            color: #000;
        }

        .subtitle {
            text-align: center;
            font-style: italic;
            color: #666;
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        h2 {
            border-left: 4px solid #444;
            padding-left: 10px;
            font-size: 14pt;
            margin-top: 25px;
            background-color: #f9f9f9;
        }

        /* Tabela com cara de relatório */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th {
            background-color: #444;
            color: white;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
            border: 1px solid #333;
            padding: 8px;
        }

        td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }

        /* Efeito Zebra */
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Status badges simples */
        .status-active {
            color: green;
            font-weight: bold;
        }
        .status-inactive {
            color: #999;
        }

        /* Rodapé de data (opcional) */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 8pt;
            text-align: right;
            color: #aaa;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ $layout['title'] }}</h1>
    @if(!empty($layout['subtitle']))
        <p class="subtitle">{{ $layout['subtitle'] }}</p>
    @endif
</div>

@foreach($sections as $section)
    <h2>{{ $section['title'] }}</h2>

    @if($section['key'] === 'assistive_technologies')
        <table>
            <thead>
            <tr>
                <th style="width: 20%;">Nome</th>
                <th style="width: 40%;">Descrição</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Ativo</th>
            </tr>
            </thead>
            <tbody>
            @foreach($section['data'] as $item)
                <tr>
                    <td><strong>{{ $item->name }}</strong></td>
                    <td>{{ $item->description ?? 'Sem descrição' }}</td>
                    <td>{{ $item->type?->name ?? '-' }}</td>
                    <td>{{ $item->resourceStatus?->name ?? '-' }}</td>
                    <td class="{{ $item->is_active ? 'status-active' : 'status-inactive' }}">
                        {{ $item->is_active ? 'Sim' : 'Não' }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endforeach

<div class="footer">
    Gerado em {{ date('d/m/Y H:i') }}
</div>

</body>
</html>
