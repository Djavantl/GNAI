<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Avaliação PEI - {{ $evaluation->pei->student->person->name }}</title>
    <x-pdf.styles />
</head>
<body>
    <x-pdf.pages />

    @php
        $pei = $evaluation->pei;
        $student = $pei->student;
    @endphp

    <x-pdf.header
        title="Relatório de Avaliação do PEI"
        :meta="[
            'Aluno(a)' => $student->person->name,
            'Matrícula' => $student->registration ?? 'N/A',
            'Curso' => $pei->course->name,
            'Disciplina' => $pei->discipline->name,
        ]"
    />

    <x-pdf.section-title title="IDENTIFICAÇÃO DO PLANO REFERENTE" />
    <x-pdf.table>
        <x-pdf.row>
             <x-pdf.info-item label="PEI Nº" :value="$pei->id" /> 
             <x-pdf.info-item label="SEMESTRE/ANO" :value="$pei->semester->label ?? $pei->semester->name" /> 
             <x-pdf.info-item label="PROFESSOR REGENTE" :value="$pei->teacher_name" /> 
        </x-pdf.row>
    </x-pdf.table>

    <x-pdf.section-title title="INFORMAÇÕES DA AVALIAÇÃO" />
    <x-pdf.table>
        <x-pdf.row>
             <x-pdf.info-item label="TIPO DE AVALIAÇÃO" :value="$evaluation->evaluation_type->label()" /> 
             <x-pdf.info-item label="DATA DA AVALIAÇÃO" :value="$evaluation->evaluation_date->format('d/m/Y')" />
             <x-pdf.info-item label="PROFISSIONAL AVALIADOR" :value="$evaluation->professional->person->name" />
        </x-pdf.row>
    </x-pdf.table>

    <x-pdf.section-title title="DESENVOLVIMENTO E RESULTADOS" />
    
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

    <div class="signature-wrapper pdf-mt-30">
        <x-pdf.table-signatures>
             <x-pdf.table-signature-label label="PROFESSOR(A): {{ strtoupper($pei->teacher_name) }}" />
             <x-pdf.table-signature-label label="RESPONSÁVEL NAI / AEE: {{ strtoupper($evaluation->professional->person->name) }}" />
        </x-pdf.table-signatures>
    </div>

</body>
</html>
