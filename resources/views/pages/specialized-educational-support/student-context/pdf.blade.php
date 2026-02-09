<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - {{ $student->person->name }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
    </style>
</head>
<body>
    @php
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
        <h2>Ficha de Contexto Educacional</h2>
        <p><strong>Aluno(a):</strong> {{ $student->person->name }} | <strong>Matrícula:</strong> {{ $student->registration ?? 'N/A' }}</p>
        <p><strong>Gerado em:</strong> {{ date('d/m/Y H:i') }} | <strong>Status:</strong> {{ $context->is_current ? 'REGISTRO ATUAL' : 'HISTÓRICO' }}</p>
    </div>

    <x-pdf.section-title title="1. Identificação Técnica" />
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Tipo de Avaliação" :value="$map['eval'][$context->evaluation_type] ?? $context->evaluation_type" colspan="2" />
            <x-pdf.info-item label="Última Atualização" :value="$context->updated_at->format('d/m/Y H:i')" colspan="2" />
        </x-pdf.row>
    </x-pdf.table>

    <x-pdf.section-title title="2. Aprendizagem e Cognição" />
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Nível Aprendizagem" :value="$map['levels'][$context->learning_level] ?? '---'" />
            <x-pdf.info-item label="Atenção" :value="$map['levels'][$context->attention_level] ?? '---'" />
            <x-pdf.info-item label="Memória" :value="$map['levels'][$context->memory_level] ?? '---'" />
            <x-pdf.info-item label="Raciocínio" :value="$map['reason'][$context->reasoning_level] ?? '---'" />
        </x-pdf.row>
    </x-pdf.table>
    <x-pdf.text-area label="Observações de Aprendizagem" :value="$context->learning_observations" />

    <x-pdf.section-title title="3. Comunicação, Interação e Comportamento" />
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Comunicação" :value="$map['comm'][$context->communication_type] ?? '---'" />
            <x-pdf.info-item label="Interação Social" :value="$map['levels'][$context->interaction_level] ?? '---'" />
            <x-pdf.info-item label="Socialização" :value="$map['social'][$context->socialization_level] ?? '---'" />
        </x-pdf.row>
        <x-pdf.row>
            <x-pdf.info-item label="Agressividade" :value="$context->shows_aggressive_behavior ? '<strong>SIM (ALERTA)</strong>' : 'Não'" />
            <x-pdf.info-item label="Retraimento" :value="$context->shows_withdrawn_behavior ? '<strong>SIM (ALERTA)</strong>' : 'Não'" />
            <x-pdf.info-item label="Intercorrências" :value="(!$context->shows_aggressive_behavior && !$context->shows_withdrawn_behavior) ? 'Nenhuma' : 'Ver notas'" />
        </x-pdf.row>
    </x-pdf.table>
    <x-pdf.text-area label="Notas de Comportamento" :value="$context->behavior_notes" />

    <x-pdf.section-title title="4. Autonomia e Apoios Necessários" />
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Nível Autonomia" :value="$map['auto'][$context->autonomy_level] ?? '---'" />
            <x-pdf.info-item label="Apoio Mobilidade" :value="$context->needs_mobility_support ? '<strong>SIM</strong>' : 'Não'" />
            <x-pdf.info-item label="Apoio Comunicação" :value="$context->needs_communication_support ? '<strong>SIM</strong>' : 'Não'" />
        </x-pdf.row>
        <x-pdf.row>
            <x-pdf.info-item label="Adaptação Pedagógica" :value="$context->needs_pedagogical_adaptation ? '<strong>SIM</strong>' : 'Não'" />
            <x-pdf.info-item label="Tecnologia Assistiva" :value="$context->uses_assistive_technology ? '<strong>SIM</strong>' : 'Não'" colspan="2" />
        </x-pdf.row>
    </x-pdf.table>

    <x-pdf.section-title title="5. Saúde e Histórico" />
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Possui Laudo Médico" :value="$context->has_medical_report ? '<strong>SIM</strong>' : 'Não'" />
            <x-pdf.info-item label="Usa Medicação" :value="$context->uses_medication ? '<strong>SIM</strong>' : 'Não'" />
        </x-pdf.row>
    </x-pdf.table>
    <x-pdf.text-area label="Observações de Saúde" :value="$context->medical_notes" />
    <x-pdf.text-area label="Histórico Escolar/Familiar" :value="$context->history" />
    <x-pdf.text-area label="Necessidades Educacionais Específicas" :value="$context->specific_educational_needs" />

    <x-pdf.section-title title="6. Avaliação Geral e Recomendações" />
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Pontos Fortes / Potencialidades" :value="$context->strengths" />
            <x-pdf.info-item label="Dificuldades Observadas" :value="$context->difficulties" />
        </x-pdf.row>
    </x-pdf.table>
    <x-pdf.text-area label="Recomendações Pedagógicas" :value="$context->recommendations" />
    <x-pdf.text-area label="Observação Geral" :value="$context->general_observation" />

    <div class="signature-wrapper">
        <x-pdf.table-signatures>
            <x-pdf.table-signature-label label="Responsável Técnico" />
            <x-pdf.table-signature-label label="Coordenação / Direção" />
        </x-pdf.table-signatures>
    </div>

    <x-pdf.pages />
</body>
</html>