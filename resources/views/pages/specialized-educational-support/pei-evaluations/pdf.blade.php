<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Avaliação PEI - {{ $evaluation->pei->student->person->name }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
        
        /* Ajustes de quebra de página */
        table, tr, td, th {
            page-break-inside: avoid !important;
        }
        
        @page {
            margin: 100px 25px;
        }
        
        footer {
            position: fixed; 
            bottom: -60px; 
            left: 0px; 
            right: 0px; 
            height: 50px; 
            text-align: center;
            font-size: 10px;
            color: #777;
        }

        .pagenum:before {
            content: counter(page);
        }

        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <footer>
        Página <span class="pagenum"></span>
    </footer>

    @php
        $pei = $evaluation->pei;
        $student = $pei->student;
    @endphp

    <div class="header">
         <h1>Relatório de Avaliação do PEI</h1>
         <p><strong>Aluno(a):</strong> {{ $student->person->name }} | <strong>Matrícula:</strong> {{ $student->registration ?? 'N/A' }}</p>
         <p><strong>Curso:</strong> {{ $pei->course->name }} | <strong>Disciplina:</strong> {{ $pei->discipline->name }}</p>
    </div>

    <x-pdf.section-title title="1. IDENTIFICAÇÃO DO PLANO REFERENTE" />
    <x-pdf.table>
        <x-pdf.row>
             <x-pdf.info-item label="PEI Nº" :value="$pei->id" /> 
             <x-pdf.info-item label="SEMESTRE/ANO" :value="$pei->semester->label ?? $pei->semester->name" /> 
             <x-pdf.info-item label="PROFESSOR REGENTE" :value="$pei->teacher_name" /> 
        </x-pdf.row>
    </x-pdf.table>

    <x-pdf.section-title title="2. INFORMAÇÕES DA AVALIAÇÃO" />
    <x-pdf.table>
        <x-pdf.row>
             <x-pdf.info-item label="TIPO DE AVALIAÇÃO" :value="$evaluation->evaluation_type->label()" /> 
             <x-pdf.info-item label="DATA DA AVALIAÇÃO" :value="$evaluation->evaluation_date->format('d/m/Y')" />
             <x-pdf.info-item label="PROFISSIONAL AVALIADOR" :value="$evaluation->professional->person->name" />
        </x-pdf.row>
    </x-pdf.table>

    <x-pdf.section-title title="3. DESENVOLVIMENTO E RESULTADOS" />
    
    <x-pdf.text-area 
        label="INSTRUMENTOS DE AVALIAÇÃO UTILIZADOS:" 
        :value="$evaluation->evaluation_instruments" 
    /> 

    <x-pdf.text-area 
        label="PARECER DESCRITIVO (DESEMPENHO DO ESTUDANTE):" 
        :value="$evaluation->parecer" 
    /> 

    <x-pdf.text-area 
        label="ESTRATÉGIAS E PROPOSTAS QUE OBTIVERAM ÊXITO:" 
        :value="$evaluation->successful_proposals" 
    /> 

    @if($evaluation->next_stage_goals)
        <x-pdf.text-area 
            label="METAS E ORIENTAÇÕES PARA A PRÓXIMA ETAPA:" 
            :value="$evaluation->next_stage_goals" 
        /> 
    @endif

    <div class="signature-wrapper" style="margin-top: 30px;">
        <x-pdf.table-signatures>
             <x-pdf.table-signature-label label="PROFESSOR(A): {{ strtoupper($pei->teacher_name) }}" />
             <x-pdf.table-signature-label label="RESPONSÁVEL NAI / AEE: {{ strtoupper($evaluation->professional->person->name) }}" />
        </x-pdf.table-signatures>
    </div>

</body>
</html>