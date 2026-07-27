<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Ficha do Aluno - {{ $student->person->name }}</title>
    <x-pdf.styles />
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

$boolLabel = fn ($value) =>
    filled($value)
        ? ($value ? 'SIM' : 'Não')
        : '<span class="text-muted">---</span>';

$boolStrong = fn ($value) =>
    filled($value)
        ? ($value ? '<strong>SIM</strong>' : 'Não')
        : '<span class="text-muted">---</span>';

$renderHtml = fn ($value) =>
    filled($value)
        ? \App\Support\RichTextSanitizer::sanitize((string) $value)
        : '<span class="text-muted">---</span>';

$renderText = fn ($value) =>
    filled($value)
        ? e($value)
        : '<span class="text-muted">---</span>';

$formatDate = fn ($date) =>
    filled($date)
        ? \Carbon\Carbon::parse($date)->format('d/m/Y')
        : '<span class="text-muted">---</span>';

$severityMap = [
    'mild' => 'Leve',
    'moderate' => 'Moderada',
    'severe' => 'Severa'
];

$relationshipMap = [
    'father' => 'Pai',
    'mother' => 'Mãe',
    'grandfather' => 'Avô',
    'grandmother' => 'Avó',
    'guardian' => 'Responsável Legal',
    'other' => 'Outro',
];

@endphp

    <x-pdf.header
        title="Ficha do Aluno"
        :status="$student->status?->label()"
        :meta="[
            'Aluno(a)' => $student->person->name ?? '---',
            'Matrícula' => $student->registration ?? '---',
            'Gerado em' => now()->format('d/m/Y H:i'),
        ]"
    />

    <div class="category-header">Informações do Aluno</div>

    {{-- ================= DADOS BÁSICOS ================= --}}
    <div class="section-title">Dados Básicos</div>
    <table class="pdf-table">
        <tr>
            <td class="pdf-cell" colspan="2">
                <strong>Nome:</strong> {!! $renderHtml($student->person->name ?? null) !!}
            </td>
            <td class="pdf-cell" colspan="2">
                <strong>Matrícula:</strong> {!! $renderHtml($student->registration ?? null) !!}
            </td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="2">
                <strong>Documento:</strong> {!! $renderHtml($student->person->document ?? null) !!}
            </td>
            <td class="pdf-cell" colspan="2">
                <strong>Data de Nascimento:</strong> {{ $formatDate($student->person->birth_date ?? null) }}
            </td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="2">
                <strong>Gênero:</strong> {{ $student->person->gender_label }}
            </td>
            <td class="pdf-cell" colspan="2">
                <strong>Status:</strong>
                {{ $student->status?->label() ?? '---' }}
            </td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="2">
                <strong>E-mail:</strong> {!! $renderHtml($student->person->email ?? null) !!}
            </td>
            <td class="pdf-cell" colspan="2">
                <strong>Telefone:</strong> {!! $renderHtml($student->person->phone ?? null) !!}
            </td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="4">
                <strong>Endereço:</strong> {!! $renderHtml($student->person->address ?? null) !!}
            </td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="2">
                <strong>Data de Ingresso:</strong> {{ $formatDate($student->entry_date ?? null) }}
            </td>
            <td class="pdf-cell" colspan="2">
                <strong>Quantidade de Perfis de Atendimento:</strong> {{ $student->deficiencies?->count() ?? 0 }}
            </td>
        </tr>
    </table>

    {{-- ================= PERFIS DE ATENDIMENTO ================= --}}
    <div class="section-title">Perfis de Atendimento</div>

    @if($student->deficiencies && $student->deficiencies->count())
        <table class="pdf-table">
            <tr>
                <th class="pdf-cell pdf-w-24">Perfil de Atendimento</th>
                <th class="pdf-cell pdf-w-16">Severidade</th>
                <th class="pdf-cell">Observações</th>
            </tr>
            @foreach($student->deficiencies as $deficiency)
                <tr>
                    <td class="pdf-cell">
                        {!! $renderHtml($deficiency->name ?? null) !!}
                    </td>
                    <td class="pdf-cell">
                        {{ $severityMap[$deficiency->pivot->severity ?? null] ?? '---' }}
                    </td>
                    <td class="pdf-cell">
                        {!! $renderHtml($deficiency->pivot->notes ?? null) !!}
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <table class="pdf-table">
            <tr>
                <td class="pdf-cell">Nenhum perfil de atendimento vinculado a este aluno.</td>
            </tr>
        </table>
    @endif

        {{-- ================= CURSO ATUAL ================= --}}
    <div class="section-title">Curso Atual</div>

    @if($student->currentCourse && $student->currentCourse->course)
        <table class="pdf-table">
            <tr>
                <td class="pdf-cell" colspan="2">
                    <strong>Curso:</strong> {!! $renderHtml($student->currentCourse->course->name ?? null) !!}
                </td>
                <td class="pdf-cell" colspan="2">
                    <strong>Ano Acadêmico:</strong> {!! $renderHtml($student->currentCourse->academic_year ?? null) !!}
                </td>
            </tr>
        </table>
    @else
        <table class="pdf-table">
            <tr>
                <td class="pdf-cell">Nenhum curso atual vinculado a este aluno.</td>
            </tr>
        </table>
    @endif

        {{-- ================= RESPONSÁVEIS ================= --}}
    <div class="section-title">Responsáveis</div>

    @if($student->guardians && $student->guardians->count())
        @foreach($student->guardians as $guardian)
            <table class="pdf-table pdf-mb-12">
                <tr>
                    <td class="pdf-cell" colspan="4">
                        <strong>Relação com o aluno:</strong> {!! $renderHtml($guardian->relationshipLabel()) !!}
                    </td>
                </tr>
                <tr>
                    <td class="pdf-cell" colspan="2">
                        <strong>Nome:</strong> {!! $renderHtml($guardian->person->name ?? null) !!}
                    </td>
                    <td class="pdf-cell" colspan="2">
                        <strong>Documento:</strong> {!! $renderHtml($guardian->person->document ?? null) !!}
                    </td>
                </tr>
                <tr>
                    <td class="pdf-cell" colspan="2">
                        <strong>Data de Nascimento:</strong> {{ $formatDate($guardian->person->birth_date ?? null) }}
                    </td>
                    <td class="pdf-cell" colspan="2">
                        <strong>Gênero:</strong> {{ $guardian->person->gender_label }}
                    </td>
                </tr>
                <tr>
                    <td class="pdf-cell" colspan="2">
                        <strong>E-mail:</strong> {!! $renderHtml($guardian->person->email ?? null) !!}
                    </td>
                    <td class="pdf-cell" colspan="2">
                        <strong>Telefone:</strong> {!! $renderHtml($guardian->person->phone ?? null) !!}
                    </td>
                </tr>
                <tr>
                    <td class="pdf-cell" colspan="4">
                        <strong>Endereço:</strong> {!! $renderHtml($guardian->person->address ?? null) !!}
                    </td>
                </tr>
            </table>
        @endforeach
    @else
        <table class="pdf-table">
            <tr>
                <td class="pdf-cell">Nenhum responsável vinculado a este aluno.</td>
            </tr>
        </table>
    @endif

    <div class="category-header">Contexto do Aluno</div>

        {{-- ================= CONTEXTO ATUAL ================= --}}
    <div class="section-title">Identificação</div>

    @if($student->currentContext)
        @php
            $context = $student->currentContext;
        @endphp

        <table class="pdf-table">
            <tr>
                <td class="pdf-cell" colspan="2">
                    <strong>Tipo de Avaliação:</strong>
                    {{ $map['eval'][$context->evaluation_type] ?? e($context->evaluation_type ?? '---') }}
                </td>
                <td class="pdf-cell" colspan="2">
                    <strong>Semestre:</strong> {!! $renderHtml($context->semester->label ?? $context->semester->name ?? null) !!}
                </td>
            </tr>
            <tr>
                <td class="pdf-cell" colspan="2">
                    <strong>Avaliado por:</strong> {!! $renderHtml($context->evaluator->person->name ?? null) !!}
                </td>
                <td class="pdf-cell" colspan="2">
                    <strong>Registro Atual:</strong> {{ $boolLabel($context->is_current) }}
                </td>
            </tr>
            <tr>
                <td class="pdf-cell" colspan="2">
                    <strong>Versão:</strong> {!! $renderHtml($context->version ?? null) !!}
                </td>
                <td class="pdf-cell" colspan="2">
                    <strong>Última Atualização:</strong> {{ optional($context->updated_at)->format('d/m/Y H:i') ?? '---' }}
                </td>
            </tr>
        </table>

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
        </table>

        <div class="section-title">Aprendizagem e Cognição</div>
        <table class="pdf-table">
            <tr>
                <td class="pdf-cell">
                    <strong>Nível de Aprendizagem:</strong> {{ $map['levels'][$context->learning_level] ?? '---' }}
                </td>
                <td class="pdf-cell">
                    <strong>Nível de Atenção:</strong> {{ $map['levels'][$context->attention_level] ?? '---' }}
                </td>
            </tr>
            <tr>
                <td class="pdf-cell">
                    <strong>Nível de Memória:</strong> {{ $map['levels'][$context->memory_level] ?? '---' }}
                </td>
                <td class="pdf-cell">
                    <strong>Nível de Raciocínio:</strong> {{ $map['reason'][$context->reasoning_level] ?? '---' }}
                </td>
            </tr>
            <tr>
                <td class="pdf-cell" colspan="4">
                    <strong>Observações de Aprendizagem</strong>
                    <div class="long-text">{!! $renderHtml($context->learning_observations) !!}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Comunicação e Comportamento</div>
        <table class="pdf-table">
            <tr>
                <td class="pdf-cell">
                    <strong>Tipo de Comunicação:</strong> {{ $map['comm'][$context->communication_type] ?? '---' }}
                </td>
                <td class="pdf-cell">
                    <strong>Nível de Interação:</strong> {{ $map['levels'][$context->interaction_level] ?? '---' }}
                </td>
            </tr>
            <tr>
                <td class="pdf-cell">
                    <strong>Nível de Socialização:</strong> {{ $map['social'][$context->socialization_level] ?? '---' }}
                </td>
                <td class="pdf-cell">
                    <strong>Comportamento Agressivo:</strong> {!! $boolStrong($context->shows_aggressive_behavior) !!}
                </td>
            </tr>
            <tr>
                <td class="pdf-cell">
                    <strong>Comportamento Retraído:</strong> {!! $boolStrong($context->shows_withdrawn_behavior) !!}
                </td>
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

        <div class="section-title">Autonomia e Apoios</div>
        <table class="pdf-table">
            <tr>
                <td class="pdf-cell">
                    <strong>Nível de Autonomia:</strong> {{ $map['auto'][$context->autonomy_level] ?? '---' }}
                </td>
                <td class="pdf-cell">
                    <strong>Apoio de Mobilidade:</strong> {!! $boolStrong($context->needs_mobility_support) !!}
                </td>
            </tr>
            <tr>
                <td class="pdf-cell">
                    <strong>Apoio de Comunicação:</strong> {!! $boolStrong($context->needs_communication_support) !!}
                </td>
                <td class="pdf-cell">
                    <strong>Adaptação Pedagógica:</strong> {!! $boolStrong($context->needs_pedagogical_adaptation) !!}
                </td>
            </tr>
            <tr>
                <td class="pdf-cell" colspan="4">
                    <strong>Tecnologia Assistiva:</strong> {!! $boolStrong($context->uses_assistive_technology) !!}
                </td>
            </tr>
        </table>

        <div class="section-title">Saúde</div>
        <table class="pdf-table">
            <tr>
                <td class="pdf-cell">
                    <strong>Possui Laudo Médico:</strong> {!! $boolStrong($context->has_medical_report) !!}
                </td>
                <td class="pdf-cell">
                    <strong>Usa Medicação:</strong> {!! $boolStrong($context->uses_medication) !!}
                </td>
            </tr>
            <tr>
                <td class="pdf-cell" colspan="4">
                    <strong>Observações Médicas</strong>
                    <div class="long-text">{!! $renderHtml($context->medical_notes) !!}</div>
                </td>
            </tr>
        </table>
    @else
        <table class="pdf-table">
            <tr>
                <td class="pdf-cell">Nenhum contexto atual encontrado para este aluno.</td>
            </tr>
        </table>
    @endif

    {{-- ========================= --}}
    {{-- PEIs e Adaptações por Disciplina --}}
    {{-- ========================= --}}
    <div class="category-header">PEIs do Aluno</div>

    <div class="divider"></div>

    @forelse ($student->peis as $pei)

        {{-- Separação entre PEIs --}}
        @if (!$loop->first)

            <div class="page-break"></div>

            <div class="pdf-divider-strong"></div>

        @endif

        {{-- ========================= --}}
        {{-- CABEÇALHO DO PEI (UMA VEZ) --}}
        {{-- ========================= --}}
        <div class="section-title">
            PLANO EDUCACIONAL INDIVIDUALIZADO (PEI) - VERSÃO {{ $pei->version }}
        </div>

        <table class="pdf-table">
            <tr>
                <td class="pdf-cell" colspan="2">
                    <strong>Estudante:</strong> {!! $renderHtml($student->person->name ?? null) !!}
                </td>
                <td class="pdf-cell" colspan="2">
                    <strong>Curso:</strong> {!! $renderHtml($pei->course->name ?? null) !!}
                </td>
            </tr>
            <tr>
                <td class="pdf-cell" colspan="2">
                    <strong>Semestre/Ano:</strong> {!! $renderHtml($pei->semester->label ?? null) !!}
                </td>
                <td class="pdf-cell" colspan="2">
                    <strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>

        {{-- ========================= --}}
        {{-- INFORMAÇÕES DO NAPNE (UMA VEZ) --}}
        {{-- ========================= --}}
        <div class="section-title">Informações de Apoio Pedagógico (NAPNE)</div>

        <table class="pdf-table">
            <tr>
                <td class="pdf-cell">
                    <strong>Histórico (Trajetória do Estudante):</strong>
                    <div class="long-text">
                        {!! $renderHtml($pei->studentContext->history) !!}
                    </div>
                </td>
            </tr>

            <tr>
                <td class="pdf-cell">
                    <strong>Necessidades Educacionais Específicas:</strong>
                    <div class="long-text">
                        {!! $renderHtml($pei->studentContext->specific_educational_needs) !!}
                    </div>
                </td>
            </tr>

            <tr>
                <td class="pdf-cell">
                    <strong>Conhecimentos e Interesses:</strong>
                    <div class="long-text">
                        {!! $renderHtml($pei->studentContext->knowledge) !!}
                    </div>
                </td>
            </tr>

            <tr>
                <td class="pdf-cell">
                    <strong>Dificuldades Apresentadas:</strong>
                    <div class="long-text">
                        {!! $renderHtml($pei->studentContext->difficulties) !!}
                    </div>
                </td>
            </tr>
        </table>

        {{-- ========================= --}}
        {{-- DISCIPLINAS DO PEI --}}
        {{-- ========================= --}}
        @foreach ($pei->peiDisciplines as $item)

            <div class="section-title">
                Adaptações Razoáveis e/ou Acessibilidades Curriculares —
                {!! $renderHtml(mb_strtoupper($item->discipline->name, 'UTF-8')) !!}
            </div>

            <table class="pdf-table">

                <tr>
                    <td class="pdf-cell" colspan="2">
                        <strong>Componente Curricular:</strong>
                        {!! $renderHtml(mb_strtoupper($item->discipline->name, 'UTF-8')) !!}
                    </td>

                    <td class="pdf-cell" colspan="2">
                        <strong>Docente:</strong>
                        {!! $renderHtml($item->teacher->person->name ?? null) !!}
                    </td>
                </tr>

                <tr>
                    <td class="pdf-cell" colspan="4">
                        <strong>Objetivos Específicos</strong>
                        <div class="long-text">
                            {!! $renderHtml($item->specific_objectives) !!}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="pdf-cell" colspan="4">
                        <strong>Conteúdos Programáticos</strong>
                        <div class="long-text">
                            {!! $renderHtml($item->content_programmatic) !!}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="pdf-cell" colspan="4">
                        <strong>Metodologia</strong>
                        <div class="long-text">
                            {!! $renderHtml($item->methodologies) !!}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="pdf-cell" colspan="4">
                        <strong>Avaliação</strong>
                        <div class="long-text">
                            {!! $renderHtml($item->evaluations) !!}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="pdf-cell" colspan="4">
                        <strong>Registros Complementares</strong>
                        <div class="long-text">
                            {!! $renderHtml($item->complementary_records) !!}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="pdf-cell" colspan="4">
                        <strong>Parecer</strong>
                        <div class="long-text">
                            {!! $renderHtml($item->opinion) !!}
                        </div>
                    </td>
                </tr>

                

            </table>

            {{-- quebra entre disciplinas --}}
            <div class="page-break"></div>

        @endforeach

    @empty
        <table class="pdf-table">
            <tr>
                <td class="pdf-cell">Nenhum PEI atual encontrado para este aluno.</td>
            </tr>
        </table>
    @endforelse

    <x-pdf.pages />
</body>
</html>
