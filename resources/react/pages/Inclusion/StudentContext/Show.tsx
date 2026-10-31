import { useRef, useState, type ReactNode } from "react";
import { router } from "@inertiajs/react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import Tabs, { type TabItem } from "@/components/Navigation/Tabs/Tabs";
import { aeeBase, studentRecordUrl } from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import type {
    StudentContextOptionKey,
    StudentContextShowProps,
} from "./StudentContext.types";
import StudentContextPage from "./StudentContextPage";
import "@/components/Students/Students.css";
import "./StudentContext.css";

type PendingAction = "delete" | "restore" | null;

function dateTime(value: string | null | undefined): string {
    if (!value) return "Não informado";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return "Não informado";

    const day = new Intl.DateTimeFormat("pt-BR", { dateStyle: "short" }).format(date);
    const time = new Intl.DateTimeFormat("pt-BR", { timeStyle: "short" }).format(date);

    return `${day} às ${time}`;
}

function LongText({
    value,
    fallback = "Não informado",
}: {
    value: string | null | undefined;
    fallback?: string;
}): React.JSX.Element {
    const text = value?.trim();

    return text ? (
        <span className="studentContextShow__text">{text}</span>
    ) : (
        <span className="studentContextShow__empty">{fallback}</span>
    );
}

function YesNo({ value }: { value: boolean | null | undefined }): React.JSX.Element {
    return (
        <TableLabel tone={value ? "success" : "inverse"}>
            {value ? "Sim" : "Não"}
        </TableLabel>
    );
}

function Section({
    id,
    title,
    iconClass,
    children,
}: {
    id: string;
    title: string;
    iconClass: string;
    children: ReactNode;
}): React.JSX.Element {
    return (
        <FormSection
            id={id}
            title={title}
            variant="inline"
            iconClass={iconClass}
        >
            <dl className="studentContextShow__grid">
                {children}
            </dl>
        </FormSection>
    );
}

export default function InclusionStudentContextShowPage({
    studentContext: context,
    student,
    studentProfile,
    studentContextOptions,
    evaluationTypeLabel,
    can,
}: StudentContextShowProps): React.JSX.Element {
    const [pending, setPending] = useState<PendingAction>(null);
    const [processing, setProcessing] = useState(false);
    const busy = useRef(false);
    const contextUrl = `${aeeBase}/student-context/${context.id}`;
    const returnUrl = studentRecordUrl(student.id, "current-context");
    const evaluatorName = context.evaluator?.person?.name || "Não informado";
    const option = (key: StudentContextOptionKey, value: string | null): string => (
        value ? studentContextOptions[key]?.[value] ?? value : "Não informado"
    );

    function run(action: Exclude<PendingAction, null>): void {
        if (busy.current) return;
        busy.current = true;
        setProcessing(true);

        const options = {
            preserveScroll: true,
            onSuccess: () => setPending(null),
            onNetworkError: () => {
                notify({
                    tone: "error",
                    title: "Falha de conexão",
                    text: "Não foi possível conectar ao servidor. Confira sua conexão e tente novamente.",
                });
                return false;
            },
            onHttpException: () => {
                notify({
                    tone: "error",
                    title: "Não foi possível concluir",
                    text: action === "delete"
                        ? "Não foi possível excluir o contexto do aluno. Tente novamente."
                        : "Não foi possível restaurar esta versão do contexto. Tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
                setProcessing(false);
            },
        };

        if (action === "delete") {
            router.delete(contextUrl, options);
        } else {
            router.post(`${contextUrl}/restore`, {}, options);
        }
    }

    const tabs: TabItem[] = [
        {
            id: "evaluation",
            label: "Avaliação",
            icon: "fa fa-id-card-o",
            content: (
                <>
                    <Section
                        id="student-context-evaluation"
                        title="Identificação da Avaliação"
                        iconClass="fa fa-id-card-o"
                    >
                        <DetailField label="Semestre">
                            {context.semester?.label || "Não informado"}
                        </DetailField>
                        <DetailField label="Tipo de Avaliação">
                            {evaluationTypeLabel || "Não informado"}
                        </DetailField>
                        <DetailField label="Versão">
                            <strong>v{context.version}</strong>
                        </DetailField>
                        <DetailField label="Contexto Atual?">
                            <YesNo value={context.is_current} />
                        </DetailField>
                    </Section>
                    <FormSection
                        id="student-context-profiles"
                        title="Perfis de Atendimento"
                        variant="inline"
                        iconClass="fa fa-wheelchair"
                    >
                        <ul
                            className="studentContextForm__deficiencies studentContextShow__deficiencies"
                            aria-label="Perfis de atendimento"
                        >
                            {studentProfile.deficiencies.length > 0 ? (
                                studentProfile.deficiencies.map((deficiency) => (
                                    <li
                                        key={deficiency.id}
                                        className="studentContextForm__deficiency"
                                    >
                                        <strong>{deficiency.name || "Não informado"}</strong>
                                        <span>
                                            GRAU: {deficiency.severity_label ?? deficiency.severity ?? "Não informado"}
                                        </span>
                                    </li>
                                ))
                            ) : (
                                <li className="studentContextForm__deficiency studentContextForm__deficiency--empty">
                                    Nenhum perfil de atendimento registrado para {studentProfile.name || "este aluno"}.
                                </li>
                            )}
                        </ul>
                    </FormSection>
                    <Section
                        id="student-context-system"
                        title="Informações do Sistema"
                        iconClass="fa fa-info-circle"
                    >
                        <DetailField label="Profissional Avaliador">
                            {evaluatorName}
                        </DetailField>
                        <DetailField label="Criado em">
                            {dateTime(context.created_at)}
                        </DetailField>
                        <DetailField label="Última Atualização">
                            {dateTime(context.updated_at)}
                        </DetailField>
                    </Section>
                </>
            ),
        },
        {
            id: "history",
            label: "Histórico e necessidades",
            accessibleLabel: "Histórico e Necessidades Educacionais",
            icon: "fa fa-history",
            content: (
                <Section
                    id="student-context-history"
                    title="Histórico e Necessidades Educacionais"
                    iconClass="fa fa-history"
                >
                    <DetailField label="Histórico do Aluno" wide>
                        <LongText value={context.history} />
                    </DetailField>
                    <DetailField label="Necessidades Educacionais Específicas" wide>
                        <LongText value={context.specific_educational_needs} />
                    </DetailField>
                </Section>
            ),
        },
        {
            id: "synthesis",
            label: "Síntese avaliativa",
            icon: "fa fa-clipboard",
            content: (
                <Section
                    id="student-context-synthesis"
                    title="Síntese Avaliativa"
                    iconClass="fa fa-clipboard"
                >
                    <DetailField label="Conhecimentos e Interesses" wide>
                        <LongText value={context.knowledge} />
                    </DetailField>
                    <DetailField label="Dificuldades" wide>
                        <LongText value={context.difficulties} />
                    </DetailField>
                </Section>
            ),
        },
        {
            id: "learning",
            label: "Aprendizagem e cognição",
            icon: "fa fa-graduation-cap",
            content: (
                <Section
                    id="student-context-learning"
                    title="Aprendizagem e Cognição"
                    iconClass="fa fa-graduation-cap"
                >
                    <DetailField label="Nível de Aprendizagem">
                        {option("learningLevels", context.learning_level)}
                    </DetailField>
                    <DetailField label="Nível de Atenção">
                        {option("attentionLevels", context.attention_level)}
                    </DetailField>
                    <DetailField label="Nível de Memória">
                        {option("memoryLevels", context.memory_level)}
                    </DetailField>
                    <DetailField label="Nível de Raciocínio">
                        {option("reasoningLevels", context.reasoning_level)}
                    </DetailField>
                    <DetailField label="Observações de Aprendizagem" wide>
                        <LongText
                            value={context.learning_observations}
                            fallback="Sem observações registradas."
                        />
                    </DetailField>
                </Section>
            ),
        },
        {
            id: "behavior",
            label: "Comunicação e comportamento",
            icon: "fa fa-comments",
            content: (
                <Section
                    id="student-context-behavior"
                    title="Comunicação e Comportamento"
                    iconClass="fa fa-comments"
                >
                    <DetailField label="Tipo de Comunicação">
                        {option("communicationTypes", context.communication_type)}
                    </DetailField>
                    <DetailField label="Nível de Interação">
                        {option("interactionLevels", context.interaction_level)}
                    </DetailField>
                    <DetailField label="Nível de Socialização">
                        {option("socializationLevels", context.socialization_level)}
                    </DetailField>
                    <DetailField label="Comportamento Agressivo">
                        <YesNo value={context.shows_aggressive_behavior} />
                    </DetailField>
                    <DetailField label="Comportamento Retraído">
                        <YesNo value={context.shows_withdrawn_behavior} />
                    </DetailField>
                    <DetailField label="Notas Comportamentais" wide>
                        <LongText value={context.behavior_notes} />
                    </DetailField>
                </Section>
            ),
        },
        {
            id: "autonomy",
            label: "Autonomia e apoios",
            icon: "fa fa-life-ring",
            content: (
                <Section
                    id="student-context-autonomy"
                    title="Autonomia e Apoios"
                    iconClass="fa fa-life-ring"
                >
                    <DetailField label="Nível de Autonomia" wide>
                        {option("autonomyLevels", context.autonomy_level)}
                    </DetailField>
                    <DetailField label="Apoio de Mobilidade">
                        <LongText
                            value={context.needs_mobility_support}
                            fallback="Não informado"
                        />
                    </DetailField>
                    <DetailField label="Apoio de Comunicação">
                        <LongText
                            value={context.needs_communication_support}
                            fallback="Não informado"
                        />
                    </DetailField>
                    <DetailField label="Adaptação Pedagógica">
                        <LongText
                            value={context.needs_pedagogical_adaptation}
                            fallback="Não informado"
                        />
                    </DetailField>
                    <DetailField label="Tecnologia Assistiva">
                        <LongText
                            value={context.uses_assistive_technology}
                            fallback="Não informado"
                        />
                    </DetailField>
                </Section>
            ),
        },
        {
            id: "health",
            label: "Saúde",
            icon: "fa fa-heartbeat",
            content: (
                <Section
                    id="student-context-health"
                    title="Saúde"
                    iconClass="fa fa-heartbeat"
                >
                    <DetailField label="Possui Laudo Médico">
                        <YesNo value={context.has_medical_report} />
                    </DetailField>
                    <DetailField label="Usa Medicação">
                        <YesNo value={context.uses_medication} />
                    </DetailField>
                    <DetailField label="Observações Médicas" wide>
                        <LongText value={context.medical_notes} />
                    </DetailField>
                </Section>
            ),
        },
    ];

    return (
        <StudentContextPage
            title="Visualizar Contexto Educacional"
            subtitle={`Detalhes do registro de contexto para o aluno(a) ${studentProfile.name}.`}
            studentId={student.id}
            studentName={studentProfile.name}
            contextId={context.id}
            view
        >
            <div
                className="studentContextShow"
                aria-busy={processing}
            >
                <CardBox className="studentsPage__summaryCard">
                    <FormSection
                        id="student-context-identification"
                        title="Identificação do Aluno"
                        variant="inline"
                        iconClass="fa fa-user"
                    >
                        <div className="studentsPage__summary">
                            <img
                                src={studentProfile.photo_url}
                                alt={`Foto de ${studentProfile.name}`}
                                className="studentsPage__summaryPhoto"
                            />
                            <div className="studentsPage__summaryBody">
                                <p className="studentsPage__summaryName">{studentProfile.name}</p>
                                <dl className="studentContextShow__summary">
                                    <DetailField label="Matrícula">
                                        {studentProfile.registration || "Não informado"}
                                    </DetailField>
                                    <DetailField label="Status">
                                        <TableLabel tone={studentProfile.status_color || "inverse"}>
                                            {studentProfile.status_label || "Não informado"}
                                        </TableLabel>
                                    </DetailField>
                                    <DetailField label="Tipo de Avaliação">
                                        {evaluationTypeLabel || "Não informado"}
                                    </DetailField>
                                    <DetailField label="Versão">
                                        <span className="studentContextShow__version">
                                            <strong>v{context.version}</strong>
                                            <TableLabel tone={context.is_current ? "success" : "inverse"}>
                                                {context.is_current ? "Atual" : "Histórico"}
                                            </TableLabel>
                                        </span>
                                    </DetailField>
                                    <DetailField label="Data">
                                        {dateTime(context.created_at)}
                                    </DetailField>
                                    <DetailField label="Profissional Avaliador">
                                        {evaluatorName}
                                    </DetailField>
                                </dl>
                            </div>
                        </div>
                    </FormSection>
                </CardBox>
                <div className="studentsPage__recordTabs">
                    <Tabs
                        items={tabs}
                        initialActiveId="evaluation"
                        variant="vertical"
                    />
                </div>
                <FormActions
                    secondary={(
                        <Button
                            href={`${contextUrl}/pdf`}
                            target="_blank"
                            rel="noopener noreferrer"
                            tone="dark-red"
                            leftIconClass="fa fa-file-pdf-o"
                            label="PDF"
                            title="Gerar PDF do contexto do aluno"
                            aria-label="Gerar PDF do contexto do aluno"
                        />
                    )}
                    status={processing
                        ? (pending === "delete"
                            ? "Excluindo contexto do aluno..."
                            : "Restaurando versão do contexto...")
                        : undefined}
                >
                    <Button
                        href={returnUrl}
                        tone="cancel"
                        label="Voltar"
                        title="Voltar para o prontuário do aluno"
                        aria-label="Voltar para o prontuário do aluno"
                        disabled={processing}
                    />
                    {can.restore ? (
                        <Button
                            type="button"
                            tone="info"
                            leftIconClass="fa fa-history"
                            label="Restaurar esta Versão"
                            title="Restaurar esta versão como contexto atual"
                            aria-label="Restaurar esta versão como contexto atual"
                            disabled={processing}
                            onClick={() => setPending("restore")}
                        />
                    ) : null}
                    {can.update ? (
                        <Button
                            href={`${contextUrl}/edit`}
                            tone="warning"
                            leftIconClass="fa fa-pencil"
                            label="Editar"
                            title="Editar contexto do aluno"
                            aria-label="Editar contexto do aluno"
                        />
                    ) : null}
                    {can.delete ? (
                        <Button
                            type="button"
                            tone="dark-red"
                            leftIconClass="fa fa-trash-o"
                            label="Excluir"
                            title="Excluir contexto do aluno"
                            aria-label="Excluir contexto do aluno"
                            disabled={processing}
                            onClick={() => setPending("delete")}
                        />
                    ) : null}
                </FormActions>
            </div>
            <ConfirmActionModal
                open={pending === "restore"}
                title="Restaurar Versão do Contexto"
                message="Tem certeza que deseja restaurar esta versão como sendo a atual?"
                processing={processing}
                confirmLabel="Confirmar restauração"
                processingLabel="Restaurando versão..."
                confirmTone="info"
                onClose={() => setPending(null)}
                onConfirm={() => run("restore")}
            />
            <ConfirmActionModal
                open={pending === "delete"}
                title="Excluir Contexto do Aluno"
                message="ATENÇÃO: Deseja realmente excluir este registro de contexto?"
                processing={processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo contexto..."
                confirmTone="dark-red"
                onClose={() => setPending(null)}
                onConfirm={() => run("delete")}
            />
        </StudentContextPage>
    );
}
