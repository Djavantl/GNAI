<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Atendimento Pedagógico - ID #{{ $pedagogicalRecord->id }}</title>

    <x-pdf.styles />
</head>
<body>
    <x-pdf.header
        title="Registro de Atendimento Pedagógico"
        :status="$pedagogicalRecord->is_present ? 'Presente' : 'Ausente'"
        :meta="[
            'Aluno' => $student?->person?->name ?? 'Não informado',
            'Profissional' => $professional?->person?->name ?? 'Não informado',
            'Agendamento' => '#' . $session->id,
            'Data' => $session->session_date->format('d/m/Y'),
            'Horário' => \Carbon\Carbon::parse($session->start_time)->format('H:i') . ' às ' . ($session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '--:--'),
            'Duração registrada' => $pedagogicalRecord->duration,
        ]"
    />

    <x-pdf.section-title title="Planejamento e Execução" />

    @if(!$pedagogicalRecord->is_present)
        <x-pdf.text-area
            label="Motivo da Ausência"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->absence_reason ?? 'Não informado.'))"
        />
    @else
        <x-pdf.text-area
            label="Atividades Planejadas/Realizadas"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) $pedagogicalRecord->planned_performed_activities)"
        />

        <x-pdf.text-area
            label="Registro Pedagógico"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) $pedagogicalRecord->pedagogical_record)"
        />

        <x-pdf.section-title title="Complementos" />

        <x-pdf.text-area
            label="Recursos Utilizados"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->resources_used ?? 'N/A'))"
        />

        <x-pdf.text-area
            label="Observações Gerais"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->general_observations ?? 'N/A'))"
        />
    @endif

    <div class="signature-wrapper pdf-mt-50">
        <x-pdf.table-signatures>
            <x-pdf.table-signature-label label="Profissional Responsável" />
            <x-pdf.table-signature-label label="Coordenação / Direção" />
        </x-pdf.table-signatures>
    </div>

</body>
</html>
