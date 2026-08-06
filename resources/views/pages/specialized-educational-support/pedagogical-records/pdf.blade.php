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
    />

    <div class="category-header">Informações do Atendimento</div>

    <div class="section-title">Identificação</div>
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Estudante" :value="$student?->person?->name ?? 'Não informado'" colspan="2" />
            <x-pdf.info-item label="Matrícula" :value="$student?->registration ?? 'Não informada'" colspan="2" />
        </x-pdf.row>
        <x-pdf.row>
            <x-pdf.info-item label="Curso" :value="$student?->currentCourse?->course?->name ?? 'Não informado'" colspan="2" />
            <x-pdf.info-item label="Profissional" :value="$professional?->person?->name ?? 'Não informado'" colspan="2" />
        </x-pdf.row>
        <x-pdf.row>
            <x-pdf.info-item label="Data do Atendimento" :value="$session->session_date->format('d/m/Y')" />
            <x-pdf.info-item label="Horário" :value="\Carbon\Carbon::parse($session->start_time)->format('H:i') . ' às ' . ($session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '--:--')" />
            <x-pdf.info-item label="Duração" :value="$pedagogicalRecord->duration" />
            <x-pdf.info-item label="Presença" :value="$pedagogicalRecord->is_present ? 'Presente' : 'Ausente'" />
        </x-pdf.row>
        <x-pdf.row>
            <x-pdf.info-item label="Agendamento" :value="'#' . $session->id" colspan="2" />
            <x-pdf.info-item label="Gerado em" :value="date('d/m/Y H:i')" colspan="2" />
        </x-pdf.row>
        @if($pedagogicalRecord->guardians->isNotEmpty())
            <x-pdf.row>
                <x-pdf.info-item label="Responsáveis participantes" :value="$pedagogicalRecord->guardian_names" colspan="4" />
            </x-pdf.row>
        @endif
    </x-pdf.table>

    <x-pdf.section-title title="Acompanhamento Pedagógico" />


    <x-pdf.text-area
        label="Motivo do Acompanhamento"
        :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->follow_up_reason ?? 'Não informado.'))"
    />

    @if(!$pedagogicalRecord->is_present)
        <x-pdf.table>
            <x-pdf.row>
                <x-pdf.info-item
                    label="Motivo da Ausência"
                    :value="$pedagogicalRecord->absence_reason ?? 'Não informado.'"
                    colspan="4"
                />
            </x-pdf.row>
        </x-pdf.table>
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
            label="Encaminhamentos Realizados"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->referrals_made ?? 'N/A'))"
        />

        <x-pdf.text-area
            label="Observações Complementares"
            :value="\App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($pedagogicalRecord->complementary_observations ?? 'N/A'))"
        />
    @endif

    <div class="signature-wrapper">
        <x-pdf.table-signatures>
            <x-pdf.table-signature-label label="Profissional Responsável" />
            <x-pdf.table-signature-label label="Coordenação / Direção" />
        </x-pdf.table-signatures>
    </div>

</body>
</html>
