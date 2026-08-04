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
        ]"
    />

    <x-pdf.section-title title="Acompanhamento Pedagógico" />

    <x-pdf.text-area
        label="Motivo do Acompanhamento"
        :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->follow_up_reason ?? 'Não informado.'))"
    />

    <x-pdf.text-area
        label="Situação do Acompanhamento"
        :value="$pedagogicalRecord->follow_up_status->label()"
    />

    <x-pdf.text-area
        label="Período/Duração do Acompanhamento"
        :value="$pedagogicalRecord->duration"
    />

    <x-pdf.text-area
        label="Presença do Estudante no Atendimento"
        :value="$pedagogicalRecord->is_present ? 'Presente' : 'Ausente'"
    />

    @if(!$pedagogicalRecord->is_present)
        <x-pdf.text-area
            label="Motivo da Ausência"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->absence_reason ?? 'Não informado.'))"
        />
    @else
        <x-pdf.text-area
            label="Registro do Acompanhamento Pedagógico Sistemático"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) $pedagogicalRecord->systematic_pedagogical_follow_up_record)"
        />

        <x-pdf.text-area
            label="Estratégias e Recursos Adotados (quando necessário)"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->strategies_and_resources_adopted ?? 'N/A'))"
        />

        <x-pdf.text-area
            :label="'Disciplinas com Reprovação no Curso ' . ($student?->currentCourse?->course?->name ?? 'Atual do Estudante')"
            :value="$pedagogicalRecord->failedDisciplineNames ?: 'N/A'"
        />

        <x-pdf.text-area
            :label="'Disciplinas com Risco de Insucesso Acadêmico no Curso ' . ($student?->currentCourse?->course?->name ?? 'Atual do Estudante')"
            :value="$pedagogicalRecord->atRiskDisciplineNames ?: 'N/A'"
        />

        <x-pdf.text-area
            label="Situação da Frequência Escolar"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->school_attendance_status ?? 'N/A'))"
        />

        <x-pdf.text-area
            label="Encaminhamentos Realizados"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->referrals_made ?? 'N/A'))"
        />

        <x-pdf.text-area
            label="Observações Complementares"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->complementary_observations ?? 'N/A'))"
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
