import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import Tabs, { type TabItem } from "@/components/Navigation/Tabs/Tabs";
import StudentRecordReturnButton from "@/components/Students/StudentRecordReturnButton";
import {
    aeeBase,
    dateLabel,
    studentRecordUrl,
    studentUrl,
    studentsBase,
} from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import DetailField from "@/components/UI/DetailField/DetailField";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import PeiConfirmAction from "./PeiConfirmAction";
import PeiDisciplineTable from "./PeiDisciplineTable";
import PeiStudentContextDetails from "./PeiStudentContextDetails";
import type { PeiShowProps, PeiShowRecord } from "./PeiShow.types";
import "@/components/Students/Students.css";
import "./PeiShow.css";

function PeiStatus({
    pei,
}: {
    pei: PeiShowRecord;
}): React.JSX.Element {
    return (
        <TableLabel tone={pei.is_finished ? "success" : "warning"}>
            {pei.is_finished ? "Finalizado" : "Em preenchimento"}
        </TableLabel>
    );
}

export default function InclusionPeisShowPage({
    pei,
    student,
    studentContext,
    peiDisciplines,
    activeTab,
    can,
}: PeiShowProps): React.JSX.Element {
    const peiUrl = `${aeeBase}/peis/${pei.id}`;
    const versionLabel = `v${pei.version ?? "Não informado"}`;
    const tabs: TabItem[] = [
        {
            id: "details",
            label: "Dados do PEI",
            icon: "fa fa-file-text-o",
            content: (
                <FormSection
                    id="pei-details"
                    title="Dados do PEI"
                    iconClass="fa fa-file-text-o"
                    variant="inline"
                >
                    <dl className="peiShow__grid peiShow__grid--3">
                        <DetailField label="Responsável">
                            {pei.responsible}
                        </DetailField>
                        <DetailField label="Curso">
                            {pei.course_name || "Não informado"}
                        </DetailField>
                        <DetailField label="Semestre">
                            {pei.semester_label || "Não informado"}
                        </DetailField>
                        <DetailField label="Versão">
                            <strong>{versionLabel}</strong>
                        </DetailField>
                        <DetailField label="Criado em">
                            {dateLabel(pei.created_at)}
                        </DetailField>
                        <DetailField label="Status">
                            <PeiStatus pei={pei} />
                        </DetailField>
                        <DetailField label="Perfis de atendimento" wide>
                            {student.deficiencies.length ? (
                                <ul className="peiShow__tags">
                                    {student.deficiencies.map((name) => (
                                        <li key={name}>{name}</li>
                                    ))}
                                </ul>
                            ) : "Nenhum perfil de atendimento registrado."}
                        </DetailField>
                    </dl>
                </FormSection>
            ),
        },
    ];

    if (can.viewContext) {
        tabs.push({
            id: "context",
            label: "Contexto do estudante",
            icon: "fa fa-user-circle-o",
            content: <PeiStudentContextDetails context={studentContext} />,
        });
    }

    if (can.viewDisciplines || can.createDiscipline) {
        tabs.push({
            id: "disciplines",
            label: "Adaptações curriculares",
            accessibleLabel: "Adaptações razoáveis e/ou acessibilidades curriculares",
            icon: "fa fa-book",
            content: (
                <PeiDisciplineTable
                    peiId={pei.id}
                    disciplines={peiDisciplines}
                    canView={can.viewDisciplines}
                    canCreate={can.createDiscipline}
                    canPdf={can.pdf}
                />
            ),
        });
    }

    const initialTab = tabs.some((tab) => tab.id === activeTab)
        ? activeTab
        : "details";

    return (
        <BusinessPage
            title="Plano Educacional Individualizado"
            subtitle={[
                `Versão ${pei.version ?? "Não informado"}`,
                pei.semester_label,
                pei.is_finished ? "Finalizado" : "Em preenchimento",
            ].filter(Boolean).join(" • ")}
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Alunos", href: studentsBase },
                { label: student.name, href: studentUrl(student.id) },
                { label: "PEIs", href: studentRecordUrl(student.id, "peis") },
                { label: `PEI #${pei.id}` },
            ]}
            className="studentsPage peiShow"
        >
            <CardBox className="studentsPage__summaryCard">
                <FormSection
                    id="pei-identification"
                    title="Identificação do PEI"
                    iconClass="fa fa-id-card-o"
                    variant="inline"
                >
                    <div className="studentsPage__summary">
                        <img
                            src={student.photo_url || "/images/default-user.jpg"}
                            alt={`Foto de ${student.name}`}
                            className="studentsPage__summaryPhoto"
                        />
                        <div className="studentsPage__summaryBody">
                            <p className="studentsPage__summaryName">{student.name}</p>
                            <dl className="peiShow__summaryDetails">
                                <DetailField label="Matrícula">
                                    {student.registration || "Não informado"}
                                </DetailField>
                                <DetailField label="Situação do aluno">
                                    <TableLabel tone={student.status_color ?? "inverse"}>
                                        {student.status_label || "Não informado"}
                                    </TableLabel>
                                </DetailField>
                                <DetailField label="Versão">
                                    <strong>{versionLabel}</strong>
                                </DetailField>
                                <DetailField label="Status do PEI">
                                    <PeiStatus pei={pei} />
                                </DetailField>
                            </dl>
                        </div>
                    </div>
                </FormSection>
            </CardBox>
            <div className="studentsPage__recordTabs">
                <Tabs
                    items={tabs}
                    initialActiveId={initialTab}
                    variant="vertical"
                />
            </div>
            <FormActions
                secondary={can.pdf ? (
                    <Button
                        href={`${peiUrl}/pdf`}
                        target="_blank"
                        rel="noopener noreferrer"
                        tone="dark-red"
                        leftIconClass="fa fa-file-pdf-o"
                        label="PDF"
                        title="Gerar PDF completo do PEI"
                        aria-label="Gerar PDF completo do PEI"
                    />
                ) : undefined}
            >
                <StudentRecordReturnButton
                    studentId={student.id}
                    tab="peis"
                />
                {can.finish ? (
                    <PeiConfirmAction
                        method="patch"
                        action={`${peiUrl}/finish`}
                        label="Finalizar"
                        ariaLabel="Finalizar PEI"
                        tone="success"
                        iconClass="fa fa-check"
                        modalTitle="Finalizar PEI"
                        message="Após finalizar, o plano não poderá ser editado. Confirmar?"
                        confirmLabel="Confirmar finalização"
                        processingLabel="Finalizando PEI..."
                    />
                ) : null}
                {can.delete ? (
                    <PeiConfirmAction
                        method="delete"
                        action={peiUrl}
                        label="Excluir"
                        ariaLabel="Excluir PEI"
                        tone="dark-red"
                        iconClass="fa fa-trash-o"
                        modalTitle="Excluir PEI"
                        message="Excluir permanentemente este PEI?"
                        confirmLabel="Confirmar exclusão"
                        processingLabel="Excluindo PEI..."
                    />
                ) : null}
            </FormActions>
        </BusinessPage>
    );
}
