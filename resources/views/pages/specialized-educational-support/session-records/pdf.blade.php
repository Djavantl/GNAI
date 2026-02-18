<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Registro de Sessão - ID #{{ $sessionRecord->id }}</title>

    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
        .absence-badge {
            color: #d9534f;
            font-weight: bold;
            text-transform: uppercase;
        }
        .student-header {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #333;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Registro de Atendimento Educacional Especializado</h2>

        <p>
            <strong>Profissional:</strong> {{ $professional->person->name ?? 'Não informado' }}
            |
            <strong>Sessão:</strong> #{{ $session->id }}
        </p>

        <p>
            <strong>Data da Sessão:</strong> {{ $session->session_date->format('d/m/Y') }}
            |
            <strong>Duração:</strong> {{ $sessionRecord->duration }}
        </p>

        <p>
            <strong>Gerado em:</strong> {{ date('d/m/Y H:i') }}
        </p>
    </div>

    {{-- ============================ --}}
    <x-pdf.section-title title="1. Planejamento e Execução Geral" />

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
        @if(!$loop->first) <div class="page-break"></div> @endif

        <div class="student-header">
            <strong>ALUNO(A): {{ $evaluation->student->person->name }}</strong> 
            @if(!$evaluation->is_present)
                <span class="absence-badge"> - AUSENTE</span>
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
                    <x-pdf.info-item label="Ajustes Próxima Sessão" :value="$evaluation->next_session_adjustments ?? 'N/A'" />
                </x-pdf.row>
            </x-pdf.table>
        @endif

    @endforeach

    {{-- ============================ --}}
    <div class="signature-wrapper" style="margin-top: 50px;">
        <x-pdf.table-signatures>
            <x-pdf.table-signature-label label="Profissional Responsável" />
            <x-pdf.table-signature-label label="Coordenação / Direção" />
        </x-pdf.table-signatures>
    </div>

    <x-pdf.pages />

</body>
</html>