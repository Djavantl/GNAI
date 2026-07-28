<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Registro de Atendimento AEE - ID #{{ $sessionRecord->id }}</title>

    <x-pdf.styles />
</head>
<body>

    <x-pdf.header
        title="Registro de Atendimento Educacional Especializado"
    />

    <div class="category-header">Informações do Atendimento</div>

    <div class="section-title">Identificação</div>
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Profissional" :value="$professional->person->name ?? 'Não informado'" colspan="2" />
            <x-pdf.info-item label="Agendamento" :value="'#' . $session->id" colspan="2" />
        </x-pdf.row>
        <x-pdf.row>
            <x-pdf.info-item label="Data do Atendimento" :value="$session->session_date->format('d/m/Y')" />
            <x-pdf.info-item label="Duração" :value="$sessionRecord->duration" />
            <x-pdf.info-item label="Gerado em" :value="date('d/m/Y H:i')" colspan="2" />
        </x-pdf.row>
    </x-pdf.table>

    <x-pdf.section-title title="Planejamento e Execução Geral" />

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

    @if($sessionRecord->general_observations)
        <x-pdf.text-area
            label="Observações Gerais do Grupo"
            :value="$sessionRecord->general_observations"
        />
    @endif

    {{-- LOOP DE AVALIAÇÕES INDIVIDUAIS --}}
    @foreach($sessionRecord->studentEvaluations as $evaluation)
        
        {{-- Força quebra de página se houver muitos alunos para não cortar campos --}}
        @if(!$loop->first) <div class="pdf-page-break"></div> @endif

        <div class="pdf-subject-header">
            <strong>Aluno(a): {!! \App\Support\RichTextSanitizer::sanitize((string) ($evaluation->student->person->name ?? '---')) !!}</strong>
            @if(!$evaluation->is_present)
                <span class="pdf-status-danger"> — Ausente</span>
            @endif
        </div>

        @if(!$evaluation->is_present)
            {{-- EXIBIÇÃO PARA ALUNO AUSENTE --}}
            <x-pdf.table>
                <x-pdf.row>
                    <x-pdf.info-item 
                        label="Motivo da Ausência" 
                        :value="$evaluation->absence_reason ?? 'Não informado'" 
                        colspan="3"
                    />
                </x-pdf.row>
            </x-pdf.table>
        @else
            {{-- EXIBIÇÃO PARA ALUNO PRESENTE --}}
            <x-pdf.table>
                <x-pdf.row>
                    <x-pdf.info-item label="Participação" :value="$evaluation->student_participation" />
                    <x-pdf.info-item label="Status" value="Presente" />
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
                    <x-pdf.info-item label="Recomendações" :value="$evaluation->recommendations ?? 'N/A'" />
                    <x-pdf.info-item label="Ajustes Próximo Atendimento" :value="$evaluation->next_session_adjustments ?? 'N/A'" />
                </x-pdf.row>
            </x-pdf.table>
        @endif

    @endforeach

    {{-- ============================ --}}
    <div class="signature-wrapper">
        <x-pdf.table-signatures>
            <x-pdf.table-signature-label label="Profissional Responsável" />
            <x-pdf.table-signature-label label="Coordenação / Direção" />
        </x-pdf.table-signatures>
    </div>
</body>
</html>
