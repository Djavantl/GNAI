import type { ReactNode } from "react";
import FormSection from "@/components/Forms/FormSection/FormSection";
import { dateTimeLabel } from "@/components/Students/Students.utils";
import DetailField from "@/components/UI/DetailField/DetailField";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import PeiRichText from "./PeiRichText";
import type { PeiStudentContext } from "./PeiShow.types";

function Section({
    id,
    title,
    iconClass,
    columns = 2,
    children,
}: {
    id: string;
    title: string;
    iconClass: string;
    columns?: 2 | 3 | 4;
    children: ReactNode;
}): React.JSX.Element {
    return (
        <FormSection
            id={id}
            title={title}
            iconClass={iconClass}
            variant="inline"
        >
            <dl className={`peiShow__grid peiShow__grid--${columns}`}>
                {children}
            </dl>
        </FormSection>
    );
}

function YesNo({
    value,
}: {
    value: boolean;
}): React.JSX.Element {
    return (
        <TableLabel tone={value ? "success" : "inverse"}>
            {value ? "Sim" : "Não"}
        </TableLabel>
    );
}

export default function PeiStudentContextDetails({
    context,
}: {
    context: PeiStudentContext | null;
}): React.JSX.Element {
    if (!context) {
        return (
            <p className="peiShow__empty">
                Nenhum contexto do estudante vinculado a este PEI.
            </p>
        );
    }

    return (
        <div className="peiShow__context">
            <Section
                id="pei-context-identification"
                title="Identificação da Avaliação"
                iconClass="fa fa-id-card-o"
                columns={4}
            >
                <DetailField label="Semestre">
                    {context.semester_label || "Não informado"}
                </DetailField>
                <DetailField label="Tipo de Avaliação">
                    {context.evaluation_type_label || "Não informado"}
                </DetailField>
                <DetailField label="Versão">
                    v{context.version ?? "Não informado"}
                </DetailField>
                <DetailField label="Contexto Atual?">
                    <TableLabel tone={context.is_current ? "success" : "inverse"}>
                        {context.is_current ? "Sim" : "Não"}
                    </TableLabel>
                </DetailField>
            </Section>
            <Section
                id="pei-context-history"
                title="Histórico e Necessidades Educacionais"
                iconClass="fa fa-history"
            >
                <DetailField label="Histórico do Aluno" wide>
                    <PeiRichText html={context.history_html} />
                </DetailField>
                <DetailField label="Necessidades Educacionais Específicas" wide>
                    <PeiRichText html={context.specific_educational_needs_html} />
                </DetailField>
            </Section>
            <Section
                id="pei-context-synthesis"
                title="Síntese Avaliativa"
                iconClass="fa fa-balance-scale"
            >
                <DetailField label="Conhecimentos e Interesses">
                    <PeiRichText html={context.knowledge_html} />
                </DetailField>
                <DetailField label="Dificuldades">
                    <PeiRichText html={context.difficulties_html} />
                </DetailField>
            </Section>
            <Section
                id="pei-context-learning"
                title="Aprendizagem e Cognição"
                iconClass="fa fa-lightbulb-o"
                columns={4}
            >
                <DetailField label="Nível de Aprendizagem">
                    {context.learning_level_label || "Não informado"}
                </DetailField>
                <DetailField label="Nível de Atenção">
                    {context.attention_level_label || "Não informado"}
                </DetailField>
                <DetailField label="Nível de Memória">
                    {context.memory_level_label || "Não informado"}
                </DetailField>
                <DetailField label="Nível de Raciocínio">
                    {context.reasoning_level_label || "Não informado"}
                </DetailField>
                <DetailField label="Observações de Aprendizagem" wide>
                    <PeiRichText html={context.learning_observations_html} />
                </DetailField>
            </Section>
            <Section
                id="pei-context-communication"
                title="Comunicação e Comportamento"
                iconClass="fa fa-comments-o"
                columns={3}
            >
                <DetailField label="Tipo de Comunicação">
                    {context.communication_type_label || "Não informado"}
                </DetailField>
                <DetailField label="Nível de Interação">
                    {context.interaction_level_label || "Não informado"}
                </DetailField>
                <DetailField label="Nível de Socialização">
                    {context.socialization_level_label || "Não informado"}
                </DetailField>
                <DetailField label="Comportamento Agressivo">
                    <YesNo value={context.shows_aggressive_behavior} />
                </DetailField>
                <DetailField label="Comportamento Retraído">
                    <YesNo value={context.shows_withdrawn_behavior} />
                </DetailField>
                <DetailField label="Notas Comportamentais" wide>
                    <PeiRichText html={context.behavior_notes_html} />
                </DetailField>
            </Section>
            <Section
                id="pei-context-autonomy"
                title="Autonomia e Apoios"
                iconClass="fa fa-universal-access"
            >
                <DetailField label="Nível de Autonomia" wide>
                    {context.autonomy_level_label || "Não informado"}
                </DetailField>
                <DetailField label="Apoio de Mobilidade">
                    <PeiRichText html={context.needs_mobility_support_html} />
                </DetailField>
                <DetailField label="Apoio de Comunicação">
                    <PeiRichText html={context.needs_communication_support_html} />
                </DetailField>
                <DetailField label="Adaptação Pedagógica">
                    <PeiRichText html={context.needs_pedagogical_adaptation_html} />
                </DetailField>
                <DetailField label="Tecnologia Assistiva">
                    <PeiRichText html={context.uses_assistive_technology_html} />
                </DetailField>
            </Section>
            <Section
                id="pei-context-health"
                title="Saúde"
                iconClass="fa fa-medkit"
            >
                <DetailField label="Possui Laudo Médico">
                    <YesNo value={context.has_medical_report} />
                </DetailField>
                <DetailField label="Usa Medicação">
                    <YesNo value={context.uses_medication} />
                </DetailField>
                <DetailField label="Observações Médicas" wide>
                    <PeiRichText html={context.medical_notes_html} />
                </DetailField>
            </Section>
            <Section
                id="pei-context-system"
                title="Informações do Sistema"
                iconClass="fa fa-info-circle"
                columns={4}
            >
                <DetailField label="Profissional Avaliador">
                    {context.evaluator_name || "Não informado"}
                </DetailField>
                <DetailField label="Criado em">
                    {dateTimeLabel(context.created_at)}
                </DetailField>
                <DetailField label="Última Atualização">
                    {dateTimeLabel(context.updated_at)}
                </DetailField>
                <DetailField label="ID do Registro">
                    #{context.id}
                </DetailField>
            </Section>
        </div>
    );
}
