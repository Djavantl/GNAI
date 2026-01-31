<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <style>
        /* 1. CONFIGURAÇÕES GERAIS DE PÁGINA */
        @page {
            size: A4;
            margin: 0; /* Margens controladas pelo preenchimento da div */
        }

        body {
            background-color: #525659; /* Fundo cinza escuro estilo visualizador de PDF */
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            -webkit-print-color-adjust: exact; /* Garante que cores de fundo apareçam na impressão */
        }

        /* 2. O "PAPEL" NO NAVEGADOR */
        .page {
            background: white;
            width: 210mm;
            min-height: 297mm;
            padding: 2cm;
            margin: 30px auto; /* Centraliza a "folha" na tela */
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
            position: relative;
        }

        /* 3. TIPOGRAFIA E ESTILO DO RELATÓRIO */
        h1 {
            text-align: center;
            text-transform: uppercase;
            font-size: 20pt;
            margin-bottom: 5px;
            color: #1a1a1a;
            letter-spacing: 1px;
        }

        .subtitle {
            text-align: center;
            font-style: italic;
            color: #666;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            font-size: 11pt;
        }

        h2 {
            border-left: 5px solid #333;
            padding-left: 12px;
            background-color: #f8f9fa;
            font-size: 14pt;
            margin-top: 25px;
            padding-top: 5px;
            padding-bottom: 5px;
            color: #333;
        }

        /* 4. TABELA PROFISSIONAL */
        .pdf-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10pt;
        }

        .pdf-table th {
            background-color: #333;
            color: #ffffff;
            text-align: left;
            padding: 12px 8px;
            text-transform: uppercase;
            font-size: 9pt;
            border: 1px solid #1a1a1a;
        }

        .pdf-table td {
            border: 1px solid #dee2e6;
            padding: 10px 8px;
            vertical-align: top;
            line-height: 1.4;
        }

        /* Zebra e efeitos visuais */
        .pdf-table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .status-badge {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
        }

        .active { color: #28a745; }
        .inactive { color: #dc3545; }

        .footer-info {
            position: absolute;
            bottom: 2cm;
            right: 2cm;
            font-size: 8pt;
            color: #999;
        }

        /* 5. AJUSTES PARA IMPRESSÃO REAL */
        @media print {
            body { background: none; }
            .page {
                margin: 0;
                box-shadow: none;
                width: 100%;
                min-height: initial;
            }
            form, .no-print { display: none; } /* Esconde o formulário na hora de imprimir */
        }

        /* Estilo do Formulário (Apenas para tela) */
        .config-form {
            background: #fff;
            padding: 20px;
            width: 210mm;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .config-form h2, .config-form h3 { border: none; background: none; padding: 0; margin-bottom: 10px; }
        input[type="text"] { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>

<div class="no-print">
    <div class="config-form">
        <form method="POST" action="{{ route('report.reports.generate') }}">
            @csrf
            <h2>Configuração do Relatório</h2>

            <h3>Cabeçalho</h3>
            <label>Título do Relatório</label>
            <input type="text" name="header_title" value="{{ old('header_title', 'Relatório de Tecnologias Assistivas') }}">

            <label>Subtítulo</label>
            <input type="text" name="header_subtitle" value="{{ old('header_subtitle', 'Sistema Radar Inclusivo') }}">

            <h3>Conteúdo</h3>
            <label>
                <input type="checkbox" name="modules[]" value="assistive_technologies" checked>
                Tecnologias Assistivas
            </label>

            <h3>Formato</h3>
            <label><input type="radio" name="format" value="screen" checked> Visualizar na tela</label>
            <label><input type="radio" name="format" value="pdf"> Gerar PDF</label>

            <br><br>
            <button type="submit">Gerar Relatório</button>
            <button type="button" onclick="window.print()" style="background: #6c757d;">Imprimir Agora</button>
        </form>
    </div>
</div>

@if(isset($sections) && $sections->isNotEmpty())
    <div class="page">
        <h1>{{ $layout['title'] }}</h1>
        @if(!empty($layout['subtitle']))
            <p class="subtitle">{{ $layout['subtitle'] }}</p>
        @endif

        @foreach($sections as $section)
            <h2>{{ $section['title'] }}</h2>

            @if($section['key'] === 'assistive_technologies')
                <table class="pdf-table">
                    <thead>
                    <tr>
                        <th style="width: 20%;">Nome</th>
                        <th style="width: 40%;">Descrição</th>
                        <th style="width: 25%;">Categoria / Status</th>
                        <th style="width: 15%; text-align: center;">Ativo</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($section['data'] as $tech)
                        <tr>
                            <td><strong>{{ $tech->name }}</strong></td>
                            <td>{{ $tech->description ?: 'Sem descrição informada.' }}</td>
                            <td>
                                <div><strong>Tipo:</strong> {{ $tech->type?->name ?? '-' }}</div>
                                <div style="margin-top: 4px; color: #666;"><strong>Status:</strong> {{ $tech->resourceStatus?->name ?? '-' }}</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="status-badge {{ $tech->is_active ? 'active' : 'inactive' }}">
                                    {{ $tech->is_active ? 'SIM' : 'NÃO' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach

        <div class="footer-info">
            Documento gerado em {{ date('d/m/Y') }} às {{ date('H:i') }}
        </div>
    </div>
@endif

</body>
</html>
