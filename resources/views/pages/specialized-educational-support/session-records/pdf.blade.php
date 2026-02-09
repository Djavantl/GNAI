<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Registro de Sessão - {{ $student->person->name }}</title>

    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
    </style>
</head>
<body>

    <div class="header">
        <h2>Registro de Atendimento Educacional Especializado</h2>

        <p>
            <strong>Aluno(a):</strong> {{ $student->person->name }}
            |
            <strong>Sessão:</strong> #{{ $session->id }}
        </p>

        <p>
            <strong>Profissional:</strong> {{ $professional->person->name ?? 'Não informado' }}
            |
            <strong>Data do Registro:</strong> {{ \Carbon\Carbon::parse($sessionRecord->record_date)->format('d/m/Y') }}
        </p>

        <p>
            <strong>Gerado em:</strong> {{ date('d/m/Y H:i') }}
        </p>
    </div>

    {{-- ============================ --}}
    <x-pdf.section-title title="1. Informações Gerais da Sessão" />

    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Duração" :value="$sessionRecord->duration" />
            <x-pdf.info-item label="Participação do Aluno" :value="$sessionRecord->student_participation" />
            <x-pdf.info-item label="Nível de Engajamento" :value="$sessionRecord->engagement_level ?? 'Não informado'" />
        </x-pdf.row>
        <x-pdf.row>
            <x-pdf.info-item 
                label="Encaminhamento Externo" 
                :value="$sessionRecord->external_referral_needed ? '<strong>SIM</strong>' : 'Não'" 
                colspan="3"
            />
        </x-pdf.row>
    </x-pdf.table>

    {{-- ============================ --}}
    <x-pdf.section-title title="2. Atividades e Estratégias Desenvolvidas" />

    <x-pdf.text-area
        label="Atividades Realizadas"
        :value="$sessionRecord->activities_performed"
    />

    <x-pdf.text-area
        label="Estratégias Utilizadas"
        :value="$sessionRecord->strategies_used"
    />

    <x-pdf.text-area
        label="Recursos Utilizados"
        :value="$sessionRecord->resources_used"
    />

    <x-pdf.text-area
        label="Adaptações Realizadas"
        :value="$sessionRecord->adaptations_made"
    />

    {{-- ============================ --}}
    <x-pdf.section-title title="3. Comportamento e Resposta às Atividades" />

    <x-pdf.text-area
        label="Comportamento Observado"
        :value="$sessionRecord->observed_behavior"
    />

    <x-pdf.text-area
        label="Resposta do Aluno às Atividades"
        :value="$sessionRecord->response_to_activities"
    />

    {{-- ============================ --}}
    <x-pdf.section-title title="4. Avaliação do Desenvolvimento e Evolução" />

    <x-pdf.text-area
        label="Avaliação do Desenvolvimento"
        :value="$sessionRecord->development_evaluation"
    />

    <x-pdf.text-area
        label="Indicadores de Progresso"
        :value="$sessionRecord->progress_indicators"
    />

    {{-- ============================ --}}
    <x-pdf.section-title title="5. Recomendações e Planejamento" />

    <x-pdf.text-area
        label="Recomendações"
        :value="$sessionRecord->recommendations"
    />

    <x-pdf.text-area
        label="Ajustes para Próximas Sessões"
        :value="$sessionRecord->next_session_adjustments"
    />

    <x-pdf.text-area
        label="Observações Gerais"
        :value="$sessionRecord->general_observations"
    />

    {{-- ============================ --}}
    <div class="signature-wrapper">
        <x-pdf.table-signatures>
            <x-pdf.table-signature-label label="Profissional Responsável" />
            <x-pdf.table-signature-label label="Coordenação / Direção" />
        </x-pdf.table-signatures>
    </div>

    <x-pdf.pages />

</body>
</html>
