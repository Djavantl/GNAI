<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Histórico de Atendimentos AEE - {{ $student->person->name }}</title>

    <x-pdf.styles />
</head>
<body>
    <x-pdf.header title="Histórico de Atendimentos Educacionais Especializados" />

    <div class="category-header">Identificação do Aluno</div>
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Estudante" :value="$student->person->name" colspan="2" />
            <x-pdf.info-item label="Matrícula" :value="$student->registration" colspan="2" />
        </x-pdf.row>
        <x-pdf.row>
            <x-pdf.info-item label="Curso" :value="$student->currentCourse?->course?->name ?? 'Não informado'" colspan="2" />
            <x-pdf.info-item label="Total de Atendimentos" :value="$aeeEvaluations->count()" />
            <x-pdf.info-item label="Gerado em" :value="date('d/m/Y H:i')" />
        </x-pdf.row>
    </x-pdf.table>

    @forelse($aeeEvaluations as $evaluation)
        @php
            $aeeRecord = $evaluation->aeeRecord;
            $session = $aeeRecord->attendanceSession;
            $professional = $session?->professional;
        @endphp

        @if(!$loop->first)
            <div class="pdf-page-break"></div>
        @else
            <div class="pdf-mt-20"></div>
        @endif

        <div class="category-header">Atendimento AEE {{ $session->session_date->format('d/m/Y') }}</div>

        <x-pdf.section-title title="Identificação do Atendimento" />
        <x-pdf.table>
            <x-pdf.row>
                <x-pdf.info-item label="Data" :value="$session->session_date->format('d/m/Y')" />
                <x-pdf.info-item
                    label="Horário"
                    :value="\Carbon\Carbon::parse($session->start_time)->format('H:i') . ' às ' . ($session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '--:--')"
                />
                <x-pdf.info-item label="Duração" :value="$aeeRecord->duration" />
                <x-pdf.info-item label="Presença" :value="$evaluation->is_present ? 'Presente' : 'Ausente'" />
            </x-pdf.row>
            <x-pdf.row>
                <x-pdf.info-item label="Profissional" :value="$professional?->person?->name ?? 'Não informado'" colspan="4" />
            </x-pdf.row>
        </x-pdf.table>

        <x-pdf.section-title title="Planejamento e Execução Geral" />

        <x-pdf.text-area
            label="Atividades Realizadas"
            :value="$aeeRecord->activities_performed"
        />

        <x-pdf.text-area
            label="Estratégias Utilizadas"
            :value="$aeeRecord->strategies_used"
        />

        <x-pdf.text-area
            label="Recursos Utilizados"
            :value="$aeeRecord->resources_used"
        />

        @if($aeeRecord->general_observations)
            <x-pdf.text-area
                label="Observações Gerais do Grupo"
                :value="$aeeRecord->general_observations"
            />
        @endif

        <x-pdf.section-title title="Avaliação Individual" />

        @if(!$evaluation->is_present)
            <x-pdf.table>
                <x-pdf.row>
                    <x-pdf.info-item
                        label="Motivo da Ausência"
                        :value="$evaluation->absence_reason ?? 'Não informado'"
                        colspan="4"
                    />
                </x-pdf.row>
            </x-pdf.table>
        @else
            <x-pdf.table>
                <x-pdf.row>
                    <x-pdf.info-item label="Participação" :value="$evaluation->student_participation" colspan="4" />
                </x-pdf.row>
            </x-pdf.table>

            <x-pdf.text-area
                label="Adaptações Realizadas para este Aluno"
                :value="$evaluation->adaptations_made ?? 'Nenhuma adaptação específica.'"
            />

            <x-pdf.text-area
                label="Avaliação do Desenvolvimento Individual"
                :value="$evaluation->development_evaluation"
            />

            <x-pdf.text-area
                label="Indicadores de Progresso"
                :value="$evaluation->progress_indicators"
            />

            <x-pdf.table>
                <x-pdf.row>
                    <x-pdf.info-item label="Recomendações" :value="$evaluation->recommendations ?? 'N/A'" colspan="2" />
                    <x-pdf.info-item label="Ajustes Próximo Atendimento" :value="$evaluation->next_session_adjustments ?? 'N/A'" colspan="2" />
                </x-pdf.row>
            </x-pdf.table>
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
                    <x-pdf.info-item label="Atendimentos" value="Nenhum atendimento AEE encontrado para este aluno." colspan="4" />
                </x-pdf.row>
            </x-pdf.table>
        </div>
    @endforelse
</body>
</html>
