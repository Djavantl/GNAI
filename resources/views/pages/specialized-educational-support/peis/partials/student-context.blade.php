{{-- resources/views/pages/specialized-educational-support/peis/partials/student-context.blade.php --}}

<div class="card p-3 border-light shadow-sm rounded">

    {{-- ================= IDENTIFICAÇÃO DA AVALIAÇÃO ================= --}}
    <x-ui.section-header 
        target="ctx-identificacao"
        title="Identificação da Avaliação"
    />

    <div id="ctx-identificacao" class="ctx-collapsed">
        <div class="row g-3 px-3 pb-3">

            <x-ui.info-card label="Semestre" column="col-md-3"
                :value="($context->semester->label ?? $context->semester->name ?? '—')" />

            @php
                $evalTypes = [
                    'initial' => 'Inicial',
                    'periodic_review' => 'Revisão Periódica',
                    'pei_review' => 'Revisão do PEI',
                    'specific_demand' => 'Demanda Específica'
                ];
                $evaluationTypeLabel = $evalTypes[$context->evaluation_type] 
                    ?? (ucfirst(str_replace('_',' ',$context->evaluation_type ?? '—')));
            @endphp

            <x-ui.info-card label="Tipo de Avaliação" column="col-md-3"
                :value="$evaluationTypeLabel" />

            <x-ui.info-card label="Versão" column="col-md-4"
                :value="('v'.($context->version ?? '—'))" />

            <x-ui.info-card label="Contexto Atual?" column="col-md-2"
                :value="($context->is_current ? 'SIM' : 'NÃO')" />
        </div>
    </div>


    {{-- ================= HISTÓRICO E NECESSIDADES ================= --}}
    <x-ui.section-header 
        target="ctx-historico"
        title="Histórico e Necessidades Educacionais"
    />

    <div id="ctx-historico" class="ctx-collapsed">
        <div class="row px-3 pb-3">
            <div class="col-12">
                <x-ui.info-card-textarea
                    label="Histórico do Aluno"
                    :value="$context->history"
                    rows="8"
                />
            </div>

            <div class="col-12 mt-2">
                <x-ui.info-card-textarea
                    label="Necessidades Educacionais Específicas"
                    :value="$context->specific_educational_needs"
                    rows="8"
                />
            </div>
        </div>
    </div>


    {{-- ================= APRENDIZAGEM E COGNIÇÃO ================= --}}
    <x-ui.section-header 
        target="ctx-aprendizagem"
        title="Aprendizagem e Cognição"
    />

    <div id="ctx-aprendizagem" class="ctx-collapsed">
        <div class="row g-3 px-3 pb-3">

            @php
                $learnMap = ['very_low'=>'Muito Baixo', 'low'=>'Baixo', 'adequate'=>'Adequado', 'good'=>'Bom', 'excellent'=>'Excelente'];
                $attMap = ['very_low'=>'Muito Baixo', 'low'=>'Baixo', 'moderate'=>'Moderado', 'high'=>'Alto'];
                $memMap = ['low'=>'Baixa', 'moderate'=>'Moderada', 'good'=>'Boa'];
                $reasonMap = ['concrete'=>'Concreto', 'mixed'=>'Misto', 'abstract'=>'Abstrato'];
            @endphp

            <x-ui.info-card label="Nível de Aprendizagem" column="col-md-3"
                :value="$learnMap[$context->learning_level] ?? ($context->learning_level ?? '—')" />

            <x-ui.info-card label="Nível de Atenção" column="col-md-3"
                :value="$attMap[$context->attention_level] ?? ($context->attention_level ?? '—')" />

            <x-ui.info-card label="Nível de Memória" column="col-md-3"
                :value="$memMap[$context->memory_level] ?? ($context->memory_level ?? '—')" />

            <x-ui.info-card label="Nível de Raciocínio" column="col-md-3"
                :value="$reasonMap[$context->reasoning_level] ?? ($context->reasoning_level ?? '—')" />

            <div class="col-12">
                <x-ui.info-card-textarea
                    label="Observações de Aprendizagem"
                    :value="$context->learning_observations"
                    rows="6"
                />
            </div>
        </div>
    </div>


    {{-- ================= COMUNICAÇÃO E COMPORTAMENTO ================= --}}
    <x-ui.section-header 
        target="ctx-comunicacao"
        title="Comunicação e Comportamento"
    />

    <div id="ctx-comunicacao" class="ctx-collapsed">
        <div class="row g-3 px-3 pb-3">

            @php
                $commMap = ['verbal'=>'Verbal', 'non_verbal'=>'Não verbal', 'mixed'=>'Mista'];
                $intMap = ['very_low'=>'Muito Baixo', 'low'=>'Baixo', 'moderate'=>'Moderado', 'good'=>'Bom'];
                $socMap = ['isolated'=>'Isolado', 'selective'=>'Seletivo', 'participative'=>'Participativo'];
            @endphp

            <x-ui.info-card label="Tipo de Comunicação" column="col-md-4"
                :value="$commMap[$context->communication_type] ?? ($context->communication_type ?? '—')" />

            <x-ui.info-card label="Nível de Interação" column="col-md-4"
                :value="$intMap[$context->interaction_level] ?? ($context->interaction_level ?? '—')" />

            <x-ui.info-card label="Nível de Socialização" column="col-md-4"
                :value="$socMap[$context->socialization_level] ?? ($context->socialization_level ?? '—')" />

            <x-ui.info-card label="Comportamento Agressivo" column="col-md-6"
                :value="(!empty($context->shows_aggressive_behavior) ? 'Sim' : 'Não')" />

            <x-ui.info-card label="Comportamento Retraído" column="col-md-6"
                :value="(!empty($context->shows_withdrawn_behavior) ? 'Sim' : 'Não')" />

            <x-ui.info-card-textarea
                label="Notas Comportamentais"
                :value="$context->behavior_notes"
                rows="4"
            />
        </div>
    </div>


    {{-- ================= AUTONOMIA E APOIOS ================= --}}
    <x-ui.section-header 
        target="ctx-autonomia"
        title="Autonomia e Apoios"
    />

    <div id="ctx-autonomia" class="ctx-collapsed">
        <div class="row g-3 px-3 pb-3">

            @php $autMap = ['dependent'=>'Dependente', 'partial'=>'Parcial', 'independent'=>'Independente']; @endphp

            <x-ui.info-card label="Nível de Autonomia" column="col-md-4"
                :value="$autMap[$context->autonomy_level] ?? ($context->autonomy_level ?? '—')" />

            <x-ui.info-card label="Apoio de Mobilidade" column="col-md-4"
                :value="(!empty($context->needs_mobility_support) ? 'Sim' : 'Não')" />

            <x-ui.info-card label="Apoio de Comunicação" column="col-md-4"
                :value="(!empty($context->needs_communication_support) ? 'Sim' : 'Não')" />

            <x-ui.info-card label="Adaptação Pedagógica" column="col-md-6"
                :value="(!empty($context->needs_pedagogical_adaptation) ? 'Sim' : 'Não')" />

            <x-ui.info-card label="Tecnologia Assistiva" column="col-md-6"
                :value="(!empty($context->uses_assistive_technology) ? 'Sim' : 'Não')" />
        </div>
    </div>


    {{-- ================= SAÚDE ================= --}}
    <x-ui.section-header 
        target="ctx-saude"
        title="Saúde"
    />

    <div id="ctx-saude" class="ctx-collapsed">
        <div class="row g-3 px-3 pb-3">
            <x-ui.info-card label="Possui Laudo Médico" column="col-md-6"
                :value="(!empty($context->has_medical_report) ? 'Sim' : 'Não')" />

            <x-ui.info-card label="Usa Medicação" column="col-md-6"
                :value="(!empty($context->uses_medication) ? 'Sim' : 'Não')" />

            <div class="col-md-12">
                <x-ui.info-card-textarea
                    label="Observações Médicas"
                    :value="$context->medical_notes"
                    rows="4"
                />
            </div>
        </div>
    </div>


    {{-- ================= SÍNTESE AVALIATIVA ================= --}}
    <x-ui.section-header 
        target="ctx-sintese"
        title="Síntese Avaliativa"
    />

    <div id="ctx-sintese" class="ctx-collapsed">
        <div class="row g-3 px-3 pb-3">

            <div class="col-md-6">
                <x-ui.info-card-textarea label="Pontos Fortes" :value="$context->strengths" rows="4" />
            </div>

            <div class="col-md-6">
                <x-ui.info-card-textarea label="Dificuldades" :value="$context->difficulties" rows="4" />
            </div>

            <div class="col-md-6 mt-2">
                <x-ui.info-card-textarea label="Recomendações" :value="$context->recommendations" rows="4" />
            </div>

            <div class="col-md-6 mt-2">
                <x-ui.info-card-textarea label="Observação Geral" :value="$context->general_observation" rows="4" />
            </div>

        </div>
    </div>


    {{-- ================= INFORMAÇÕES DO SISTEMA ================= --}}
    <x-ui.section-header 
        target="ctx-sistema"
        title="Informações do Sistema"
    />

    <div id="ctx-sistema" class="ctx-collapsed">
        <div class="row g-3 px-3 pb-3">
            <x-ui.info-card label="Profissional Avaliador" column="col-md-4"
                :value="($context->evaluator->person->name ?? '—')" />

            <x-ui.info-card label="Criado em" column="col-md-4"
                :value="(optional($context->created_at)->format('d/m/Y \\à\\s H:i') ?? '—')" />

            <x-ui.info-card label="Última Atualização" column="col-md-4"
                :value="(optional($context->updated_at)->format('d/m/Y \\à\\s H:i') ?? '—')" />
        </div>

        <div class="border-top mt-3 pt-3">
            <div class="small text-muted">
                <i class="fas fa-fingerprint me-1"></i> ID do Registro: #{{ $context->id }}
            </div>
        </div>
    </div>

</div>