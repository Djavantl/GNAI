<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Atendimento Pedagógico - ID #{{ $pedagogicalRecord->id }}</title>

    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
    </style>
</head>
<body>
    <div class="header">
        <h1>GNAI - Gestão do Núcleo de Acessibilidade e Inclusão</h1>
        <h2>Registro de Atendimento Pedagógico</h2>

        <p>
            <strong>Aluno:</strong> {{ $student?->person?->name ?? 'Não informado' }}
            |
            <strong>Profissional:</strong> {{ $professional?->person?->name ?? 'Não informado' }}
        </p>

        <p>
            <strong>Agendamento:</strong> #{{ $session->id }}
            |
            <strong>Data:</strong> {{ $session->session_date->format('d/m/Y') }}
            |
            <strong>Horário:</strong>
            {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
            às
            {{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '--:--' }}
        </p>

        <p>
            <strong>Duração registrada:</strong> {{ $pedagogicalRecord->duration }}
            |
            <strong>Presença:</strong> {{ $pedagogicalRecord->is_present ? 'Presente' : 'Ausente' }}
        </p>
    </div>

    <x-pdf.section-title title="1. Planejamento e Execução" />

    @if(!$pedagogicalRecord->is_present)
        <x-pdf.text-area
            label="Motivo da Ausência"
            :value="\App\Support\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->absence_reason ?? 'Não informado.'))"
        />
    @else
        <x-pdf.text-area
            label="Atividades Planejadas/Realizadas"
            :value="\App\Support\RichTextSanitizer::sanitize((string) $pedagogicalRecord->planned_performed_activities)"
        />

        <x-pdf.text-area
            label="Registro Pedagógico"
            :value="\App\Support\RichTextSanitizer::sanitize((string) $pedagogicalRecord->pedagogical_record)"
        />

        <x-pdf.section-title title="2. Complementos" />

        <x-pdf.text-area
            label="Recursos Utilizados"
            :value="\App\Support\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->resources_used ?? 'N/A'))"
        />

        <x-pdf.text-area
            label="Observações Gerais"
            :value="\App\Support\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->general_observations ?? 'N/A'))"
        />
    @endif

    <div class="signature-wrapper" style="margin-top: 50px;">
        <x-pdf.table-signatures>
            <x-pdf.table-signature-label label="Profissional Responsável" />
            <x-pdf.table-signature-label label="Coordenação / Direção" />
        </x-pdf.table-signatures>
    </div>

    <x-pdf.pages />
</body>
</html>
