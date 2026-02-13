<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>PEI - {{ $pei->student->person->name }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
        
        /* Estilos para evitar espaços em branco excessivos e permitir quebras de página suaves */
        table, tr, td, th {
            page-break-inside: avoid !important;
        }
        
        .table-list { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            margin-bottom: 20px; 
            table-layout: fixed; /* Ajuda a controlar o tamanho das colunas */
        }
        
        .table-list th, .table-list td { 
            padding: 8px; 
            border: 1px solid #ddd; 
            font-size: 11px; 
            word-wrap: break-word; 
        }

        .table-list th { background-color: #f2f2f2; }

        /* Rodapé para numeração de páginas */
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
    </style>
</head>
<body>
    <footer>
        Página <span class="pagenum"></span>
    </footer>

    @php
        $context = $pei->studentContext;
        $student = $pei->student;

        $map = [
            'eval' => ['initial' => 'Inicial', 'periodic_review' => 'Periódica', 'pei_review' => 'Revisão PEI', 'specific_demand' => 'Demanda Específica'],
            'levels' => ['very_low' => 'Muito Baixo', 'low' => 'Baixo', 'adequate' => 'Adequado', 'good' => 'Bom', 'excellent' => 'Excelente', 'moderate' => 'Moderado', 'high' => 'Alto'],
            'reason' => ['concrete' => 'Concreto', 'mixed' => 'Misto', 'abstract' => 'Abstrato'],
            'comm' => ['verbal' => 'Verbal', 'non_verbal' => 'Não Verbal', 'mixed' => 'Mista'],
            'social' => ['isolated' => 'Isolado', 'selective' => 'Seletivo', 'participative' => 'Participativo'],
            'auto' => ['dependent' => 'Dependente', 'partial' => 'Parcial', 'independent' => 'Independente']
        ];
    @endphp

    <div class="header">
         <h1>Plano Educacional Individualizado (PEI)</h1>
         <p><strong>Aluno(a):</strong> {{ $student->person->name }} | <strong>Matrícula:</strong> {{ $student->registration ??  'N/A' }}</p>
         <p><strong>Curso:</strong> {{ $pei->course->name }}  |  <strong>Disciplina:</strong> {{ $pei->discipline->name }}</p>
         <p><strong>Professor(a):</strong> {{ $pei->teacher_name }} | <strong>Semestre/Ano:</strong> {{ $pei->semester->name }}</p>
    </div>

     <x-pdf.section-title title="1. IDENTIFICAÇÃO DO PLANO" />
    <x-pdf.table>
        <x-pdf.row>
             <x-pdf.info-item label="CURSO" :value="$pei->course->name" colspan="2" /> 
             <x-pdf.info-item label="STATUS DO PLANO" :value="$pei->is_finished ?  'FINALIZADO' : 'EM ANDAMENTO'" colspan="2" /> 
        </x-pdf.row>
        <x-pdf.row>
             <x-pdf.info-item label="PROFISSIONAL RESPONSÁVEL (AEE)" :value="$pei->professional->person->name ??  '---'" colspan="4" /> 
        </x-pdf.row>
    </x-pdf.table>

     <h2 style="text-align: center; color: #333; margin-top: 20px;">Ficha de Contexto Educacional</h2>

     <x-pdf.section-title title="2. IDENTIFICAÇÃO TÉCNICA DO CONTEXTO" />
    <x-pdf.table>
        <x-pdf.row>
             <x-pdf.info-item label="TIPO DE AVALIAÇÃO" :value="$map['eval'][$context->evaluation_type] ??  $context->evaluation_type" /> 
             <x-pdf.info-item label="ÚLTIMA ATUALIZAÇÃO DO CONTEXTO" :value="$context->updated_at->format('d/m/Y')" />
        </x-pdf.row>
    </x-pdf.table>

    <x-pdf.section-title title="3.  APRENDIZAGEM E COGNIÇÃO" />
    <x-pdf.table>
        <x-pdf.row>
             <x-pdf.info-item label="NÍVEL APRENDIZAGEM" :value="$map['levels'][$context->learning_level] ??  '---'" /> 
             <x-pdf.info-item label="ATENÇÃO" :value="$map['levels'][$context->attention_level] ??  '---'" /> 
             <x-pdf.info-item label="MEMÓRIA" :value="$map['levels'][$context->memory_level] ??  '---'" /> 
             <x-pdf.info-item label="RACIOCÍNIO" :value="$map['reason'][$context->reasoning_level] ?? '---'" />
        </x-pdf.row>
    </x-pdf.table>
     <x-pdf.text-area label="OBSERVAÇÕES DE APRENDIZAGEM:" :value="$context->learning_observations" /> 

     <x-pdf.section-title title="4. COMUNICAÇÃO, INTERAÇÃO E COMPORTAMENTO" />
    <x-pdf.table>
        <x-pdf.row>
             <x-pdf.info-item label="COMUNICAÇÃO" :value="$map['comm'][$context->communication_type] ??  '---'" /> 
             <x-pdf.info-item label="INTERAÇÃO SOCIAL" :value="$map['levels'][$context->interaction_level] ??  '---'" /> 
             <x-pdf.info-item label="SOCIALIZAÇÃO" :value="$map['social'][$context->socialization_level] ??  '---'" /> 
        </x-pdf.row>
    </x-pdf.table>
     <x-pdf.text-area label="NOTAS DE COMPORTAMENTO:" :value="$context->behavior_notes" /> 

     <x-pdf.section-title title="5. AUTONOMIA E SAÚDE " />
    <x-pdf.table>
        <x-pdf.row>
             <x-pdf.info-item label="NÍVEL AUTONOMIA " :value="$map['auto'][$context->autonomy_level] ??  '---'" /> 
             <x-pdf.info-item label="POSSUI LAUDO " :value="$context->has_medical_report ? 'SIM' : 'Não'" />
        </x-pdf.row>
    </x-pdf.table>
     <x-pdf.text-area label="NECESSIDADES EDUCACIONAIS ESPECÍFICAS:  " :value="$context->specific_educational_needs" /> 
     <x-pdf.text-area label="POTENCIALIDADES (PONTOS FORTES):  " :value="$context->strengths" /> 

     <x-pdf.section-title title="6. CONTEÚDO PROGRAMÁTICO ADAPTADO " />
    <table class="table-list">
        <thead>
            <tr>
                 <th width="30%">Título </th>
                 <th width="70%">Descrição da Adaptação Curricular </th>
            </tr>
        </thead>
        <tbody>
            @forelse($pei->contentProgrammatic as $content)
                <tr>
                     <td><strong>{{ $content->title }}</strong></td> 
                     <td>{{ $content->description }}</td> 
                </tr>
            @empty
                <tr><td colspan="2">Nenhum conteúdo registrado.</td></tr>
            @endforelse
        </tbody>
    </table>

     <x-pdf.section-title title="7. OBJETIVOS ESPECÍFICOS E ACOMPANHAMENTO " />
    <table class="table-list">
        <thead>
            <tr>
                 <th width="40%">Objetivo </th>
                 <th width="20%">Status</th>
                 <th width="40%">Observações de Progresso </th>
            </tr>
        </thead>
        <tbody>
            @forelse($pei->specificObjectives as $objective)
                <tr>
                     <td>{{ $objective->description }}</td> 
                     <td>{{ $objective->status }}</td> 
                    <td>{{ $objective->observations_progress ??  '---' }}</td> 
                </tr>
            @empty
                <tr><td colspan="3">Nenhum objetivo registrado.</td></tr>
            @endforelse
        </tbody>
    </table>

     <x-pdf.section-title title="8. METODOLOGIAS E RECURSOS " />
    <table class="table-list">
        <thead>
            <tr>
                 <th width="50%">Estratégia Metodológica</th>
                 <th width="50%">Recursos Pedagógicos/Acessibilidade </th>
            </tr>
        </thead>
        <tbody>
            @forelse($pei->methodologies as $method)
                <tr>
                     <td>{{ $method->description }}</td> 
                    <td>{{ $method->resources_used ??  '---' }}</td> 
                </tr>
            @empty
                <tr><td colspan="2">Nenhuma metodologia registrada.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-wrapper">
        <x-pdf.table-signatures>
             <x-pdf.table-signature-label label="PROFESSOR(A): {{ strtoupper($pei->teacher_name) }}" />
             <x-pdf.table-signature-label label="RESPONSÁVEL TÉCNICO (AEE) " />
        </x-pdf.table-signatures>
    </div>
</body>
</html>