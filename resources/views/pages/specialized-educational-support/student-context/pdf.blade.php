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
        'eval' => [
            'initial' => 'Inicial',
            'periodic_review' => 'Periódica',
            'pei_review' => 'Revisão PEI',
            'specific_demand' => 'Demanda Específica',
        ],
        'levels' => [
            'very_low' => 'Muito Baixo',
            'low' => 'Baixo',
            'adequate' => 'Adequado',
            'good' => 'Bom',
            'excellent' => 'Excelente',
            'moderate' => 'Moderado',
            'high' => 'Alto',
        ],
        'reason' => [
            'concrete' => 'Concreto',
            'mixed' => 'Misto',
            'abstract' => 'Abstrato',
        ],
        'comm' => [
            'verbal' => 'Verbal',
            'non_verbal' => 'Não Verbal',
            'mixed' => 'Mista',
        ],
        'social' => [
            'isolated' => 'Isolado',
            'selective' => 'Seletivo',
            'participative' => 'Participativo',
        ],
        'auto' => [
            'dependent' => 'Dependente',
            'partial' => 'Parcial',
            'independent' => 'Independente',
        ],
    ];

    $boolLabel = fn ($value) => $value ? 'SIM' : 'Não';
    $boolStrong = fn ($value) => $value ? '<strong>SIM</strong>' : 'Não';

    $renderHtml = fn ($value) => filled($value)
        ? $value
        : '<span class="text-muted">---</span>';

    $renderText = fn ($value) => filled($value)
        ? e($value)
        : '<span class="text-muted">---</span>';
@endphp

    <div class="header">
        <h2>Ficha de Contexto Educacional</h2>
        <p>
            <strong>Aluno(a):</strong> {{ $student->person->name }}
            | <strong>Matrícula:</strong> {{ $student->registration ?? 'N/A' }}
        </p>
        <p>
            <strong>Gerado em:</strong> {{ date('d/m/Y H:i') }}
            | <strong>Status:</strong> {{ $context->is_current ? 'REGISTRO ATUAL' : 'HISTÓRICO' }}
        </p>
    </div>

    {{-- ================= IDENTIFICAÇÃO TÉCNICA ================= --}}
    <div class="section-title">Identificação Técnica</div>
    <table class="pdf-table">
        <tr>
            <td class="pdf-cell" colspan="2">
                <strong>Tipo de Avaliação:</strong>
                {{ $map['eval'][$context->evaluation_type] ?? e($context->evaluation_type ?? '---') }}
            </td>
            <td class="pdf-cell" colspan="2">
                <strong>Última Atualização:</strong>
                {{ optional($context->updated_at)->format('d/m/Y H:i') ?? '---' }}
            </td>
        </tr>
    </table>


    {{-- ================= HISTÓRICO E NECESSIDADES EDUCACIONAIS ================= --}}
    <div class="section-title">Histórico e Necessidades Educacionais</div>
    <table class="pdf-table">
        <tr>
            <td class="pdf-cell" colspan="4">
                <strong>Histórico do Aluno</strong>
                <div class="long-text">{!! $renderHtml($context->history) !!}</div>
            </td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="4">
                <strong>Necessidades Educacionais Específicas</strong>
                <div class="long-text">{!! $renderHtml($context->specific_educational_needs) !!}</div>
            </td>
        </tr>
    </table>

    {{-- ================= SÍNTESE AVALIATIVA ================= --}}
    <div class="section-title">Síntese Avaliativa</div>
    <table class="pdf-table">
        <tr>
            <td class="pdf-cell" colspan="2">
                <strong>Conhecimentos e Interesses</strong>
                <div class="long-text">{!! $renderHtml($context->knowledge) !!}</div>
            </td>
            <td class="pdf-cell" colspan="2">
                <strong>Dificuldades</strong>
                <div class="long-text">{!! $renderHtml($context->difficulties) !!}</div>
            </td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="2">
                <strong>Recomendações</strong>
                <div class="long-text">{!! $renderHtml($context->recommendations) !!}</div>
            </td>
            <td class="pdf-cell" colspan="2">
                <strong>Observação Geral</strong>
                <div class="long-text">{!! $renderHtml($context->general_observation) !!}</div>
            </td>
        </tr>
    </table>

    {{-- ================= APRENDIZAGEM E COGNIÇÃO ================= --}}
    <div class="section-title">Aprendizagem e Cognição</div>
    <table class="pdf-table">
        <tr>
            <td class="pdf-cell"><strong>Nível de Aprendizagem:</strong> {{ $map['levels'][$context->learning_level] ?? '---' }}</td>
            <td class="pdf-cell"><strong>Nível de Atenção:</strong> {{ $map['levels'][$context->attention_level] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="pdf-cell"><strong>Nível de Memória:</strong> {{ $map['levels'][$context->memory_level] ?? '---' }}</td>
            <td class="pdf-cell"><strong>Nível de Raciocínio:</strong> {{ $map['reason'][$context->reasoning_level] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="4">
                <strong>Observações de Aprendizagem</strong>
                <div class="long-text">{!! $renderHtml($context->learning_observations) !!}</div>
            </td>
        </tr>
    </table>

    {{-- ================= COMUNICAÇÃO E COMPORTAMENTO ================= --}}
    <div class="section-title">Comunicação e Comportamento</div>
    <table class="pdf-table">
        <tr>
            <td class="pdf-cell"><strong>Tipo de Comunicação:</strong> {{ $map['comm'][$context->communication_type] ?? '---' }}</td>
            <td class="pdf-cell"><strong>Nível de Interação:</strong> {{ $map['levels'][$context->interaction_level] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="pdf-cell"><strong>Nível de Socialização:</strong> {{ $map['social'][$context->socialization_level] ?? '---' }}</td>
            <td class="pdf-cell"><strong>Comportamento Agressivo:</strong> {!! $boolStrong($context->shows_aggressive_behavior) !!}</td>
        </tr>
        <tr>
            <td class="pdf-cell"><strong>Comportamento Retraído:</strong> {!! $boolStrong($context->shows_withdrawn_behavior) !!}</td>
            <td class="pdf-cell">
                <strong>Intercorrências:</strong>
                {{ (!$context->shows_aggressive_behavior && !$context->shows_withdrawn_behavior) ? 'Nenhuma' : 'Ver notas' }}
            </td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="4">
                <strong>Notas Comportamentais</strong>
                <div class="long-text">{!! $renderHtml($context->behavior_notes) !!}</div>
            </td>
        </tr>
    </table>

    {{-- ================= AUTONOMIA E APOIOS ================= --}}
    <div class="section-title">Autonomia e Apoios</div>
    <table class="pdf-table">
        <tr>
            <td class="pdf-cell"><strong>Nível de Autonomia:</strong> {{ $map['auto'][$context->autonomy_level] ?? '---' }}</td>
            <td class="pdf-cell"><strong>Apoio de Mobilidade:</strong> {!! $boolStrong($context->needs_mobility_support) !!}</td>
        </tr>
        <tr>
            <td class="pdf-cell"><strong>Apoio de Comunicação:</strong> {!! $boolStrong($context->needs_communication_support) !!}</td>
            <td class="pdf-cell"><strong>Adaptação Pedagógica:</strong> {!! $boolStrong($context->needs_pedagogical_adaptation) !!}</td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="4"><strong>Tecnologia Assistiva:</strong> {!! $boolStrong($context->uses_assistive_technology) !!}</td>
        </tr>
    </table>

    {{-- ================= SAÚDE ================= --}}
    <div class="section-title">Saúde</div>
    <table class="pdf-table">
        <tr>
            <td class="pdf-cell"><strong>Possui Laudo Médico:</strong> {!! $boolStrong($context->has_medical_report) !!}</td>
            <td class="pdf-cell"><strong>Usa Medicação:</strong> {!! $boolStrong($context->uses_medication) !!}</td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="4">
                <strong>Observações Médicas</strong>
                <div class="long-text">{!! $renderHtml($context->medical_notes) !!}</div>
            </td>
        </tr>
    </table>

    <div class="signature-wrapper">
        <x-pdf.table-signatures>
            <x-pdf.table-signature-label label="Responsável Técnico" />
            <x-pdf.table-signature-label label="Coordenação / Direção" />
        </x-pdf.table-signatures>
    </div>

    <x-pdf.pages />
</body>
</html>