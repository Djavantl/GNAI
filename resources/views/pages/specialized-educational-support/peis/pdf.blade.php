<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>PEI - {{ $pei->student->person->name }}</title>
    <style>
        /* 1. IMPORTANTE: Use DejaVu Sans para corrigir o ç e ã */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.5;
            color: #333;
        }

        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
        
        table, tr, td, th { page-break-inside: avoid !important; }
        
        .adaptation-block {
            margin-bottom: 30px;
            border: 1px solid #eee;
            padding: 10px;
        }

        .discipline-header {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #333;
            margin-bottom: 15px;
        }

        .field-group { margin-bottom: 15px; }
        .field-label { 
            font-weight: bold; 
            font-size: 11px; 
            color: #555; 
            text-transform: uppercase;
            display: block;
            margin-bottom: 5px;
        }

        /* Ajuste para que o HTML renderizado não quebre o layout */
        .field-value { 
            font-size: 12px; 
            text-align: justify;
        }
        .field-value p { margin-bottom: 5px; }

        @page { margin: 80px 25px; }
        footer {
            position: fixed; bottom: -60px; left: 0px; right: 0px; 
            height: 50px; text-align: center; font-size: 10px; color: #777;
        }
        .pagenum:before { content: counter(page); }
    </style>
</head>
<body>
    <footer>Página <span class="pagenum"></span></footer>

    <div class="header">
        <h1>Plano Educacional Individualizado (PEI)</h1>
        <p><strong>Aluno(a):</strong> {{ $pei->student->person->name }} | <strong>Matrícula:</strong> {{ $pei->student->registration ?? 'N/A' }}</p>
        <p><strong>Curso:</strong> {{ $pei->course->name }} | <strong>Semestre/Ano:</strong> {{ $pei->semester->label }}</p>
    </div>

    <x-pdf.section-title title="1. IDENTIFICAÇÃO E CONTEXTO" />
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="CURSO" :value="$pei->course->name" colspan="2" />
            <x-pdf.info-item label="VERSÃO" :value="$pei->version ?? '1'" colspan="2" /> 
        </x-pdf.row>
        <x-pdf.row>
             <x-pdf.info-item label="PROFISSIONAL RESPONSÁVEL (AEE)" :value="$pei->professional->person->name ?? '---'" colspan="4" /> 
        </x-pdf.row>
    </x-pdf.table>

    <h2 style="text-align: center; color: #333; margin-top: 20px;">2. ADAPTAÇÕES POR DISCIPLINA</h2>

    @forelse($pei->peiDisciplines as $item)
        <div class="adaptation-block">
            <div class="discipline-header">
                <span style="font-size: 14px; font-weight: bold;">DISCIPLINA: {{ mb_strtoupper($item->discipline->name, 'UTF-8') }}</span><br>
                <span style="font-size: 11px;">Professor(a): {{ $item->teacher->person->name }}</span>
            </div>

            <div class="field-group">
                <span class="field-label">Conteúdo Programático Adaptado</span>
                <div class="field-value">{!! $item->content_programmatic !!}</div>
            </div>

            <div class="field-group">
                <span class="field-label">Objetivos Específicos</span>
                <div class="field-value">{!! $item->specific_objectives !!}</div>
            </div>

            <div class="field-group">
                <span class="field-label">Metodologias e Recursos</span>
                <div class="field-value">{!! $item->methodologies !!}</div>
            </div>

            <div class="field-group">
                <span class="field-label">Processo de Avaliação</span>
                <div class="field-value">{!! $item->evaluations !!}</div>
            </div>
        </div>
    @empty
        <p style="text-align: center;">Nenhuma adaptação curricular registrada.</p>
    @endforelse

    <div class="signature-wrapper" style="margin-top: 50px;">
        <x-pdf.table-signatures>
             <x-pdf.table-signature-label label="RESPONSÁVEL TÉCNICO (AEE)" />
             <x-pdf.table-signature-label label="COORDENAÇÃO PEDAGÓGICA" />
        </x-pdf.table-signatures>
    </div>
</body>
</html>