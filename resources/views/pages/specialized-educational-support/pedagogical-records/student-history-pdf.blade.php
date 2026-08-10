<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Histórico de Atendimentos Pedagógicos - {{ $student->person->name }}</title>

    <x-pdf.styles />
</head>
<body>
    <x-pdf.header title="Histórico de Atendimentos Pedagógicos" />

    <div class="category-header">Identificação do Aluno</div>
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Estudante" :value="$student->person->name" colspan="2" />
            <x-pdf.info-item label="Matrícula" :value="$student->registration" colspan="2" />
        </x-pdf.row>
        <x-pdf.row>
            <x-pdf.info-item label="Curso" :value="$student->currentCourse?->course?->name ?? 'Não informado'" colspan="2" />
            <x-pdf.info-item label="Total de Atendimentos" :value="$pedagogicalRecords->count()" />
            <x-pdf.info-item label="Gerado em" :value="date('d/m/Y H:i')" />
        </x-pdf.row>
    </x-pdf.table>

    @forelse($pedagogicalRecords as $pedagogicalRecord)
        @php
            $session = $pedagogicalRecord->attendanceSession;
            $professional = $session?->professional;
        @endphp

        @if(!$loop->first)
            <div class="pdf-page-break"></div>
        @else
            <div class="pdf-mt-20"></div>
        @endif

        <div class="category-header">Atendimento Pedagógico {{ $session->session_date->format('d/m/Y') }}</div>

        <x-pdf.section-title title="Identificação do Atendimento" />
        <x-pdf.table>
            <x-pdf.row>
                <x-pdf.info-item label="Data" :value="$session->session_date->format('d/m/Y')" />
                <x-pdf.info-item
                    label="Horário"
                    :value="\Carbon\Carbon::parse($session->start_time)->format('H:i') . ' às ' . ($session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '--:--')"
                />
                <x-pdf.info-item label="Duração" :value="$pedagogicalRecord->duration" />
                <x-pdf.info-item label="Presença" :value="$pedagogicalRecord->is_present ? 'Presente' : 'Ausente'" />
            </x-pdf.row>
            <x-pdf.row>
                <x-pdf.info-item label="Profissional" :value="$professional?->person?->name ?? 'Não informado'" colspan="2" />
                <x-pdf.info-item
                    label="Responsáveis participantes"
                    :value="$pedagogicalRecord->guardians->isNotEmpty() ? $pedagogicalRecord->guardian_names : 'Nenhum'"
                    colspan="2"
                />
            </x-pdf.row>
        </x-pdf.table>

        <x-pdf.section-title title="Acompanhamento Pedagógico" />

        <x-pdf.text-area
            label="Motivo do Acompanhamento"
            :value="$pedagogicalRecord->follow_up_reason ?? 'Não informado.'"
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
        @endif

        @if($pedagogicalRecord->is_present || $pedagogicalRecord->guardians->isNotEmpty())
            <x-pdf.text-area
                label="Registro do Acompanhamento Pedagógico Sistemático"
                :value="$pedagogicalRecord->systematic_pedagogical_follow_up_record"
            />

            <x-pdf.text-area
                label="Estratégias e Recursos Adotados (quando necessário)"
                :value="$pedagogicalRecord->strategies_and_resources_adopted ?? 'N/A'"
            />

            <x-pdf.text-area
                label="Encaminhamentos Realizados"
                :value="$pedagogicalRecord->referrals_made ?? 'N/A'"
            />

            <x-pdf.text-area
                label="Observações Complementares"
                :value="$pedagogicalRecord->complementary_observations ?? 'N/A'"
            />
        @endif

        <div class="signature-wrapper">
            <x-pdf.table-signatures>
                <x-pdf.table-signature-label label="Profissional Responsável" />
                <x-pdf.table-signature-label label="Coordenação / Direção" />
            </x-pdf.table-signatures>
        </div>
    @empty
        <div class="pdf-mt-20">
            <x-pdf.table>
                <x-pdf.row>
                    <x-pdf.info-item label="Atendimentos" value="Nenhum atendimento pedagógico encontrado para este aluno." colspan="4" />
                </x-pdf.row>
            </x-pdf.table>
        </div>
    @endforelse
</body>
</html>
