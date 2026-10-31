import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import StudentRecordReturnButton from "@/components/Students/StudentRecordReturnButton";
import { dateTimeLabel, studentsBase } from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import StudentDeficiencyPage from "./StudentDeficiencyPage";
import type {
    StudentDeficiencyShowRecord,
    StudentDeficiencyStudent,
} from "./StudentDeficiency.types";
import "./StudentDeficiencies.css";

export default function InclusionStudentDeficienciesShowPage({
    student,
    deficiency,
    can,
}: {
    student: StudentDeficiencyStudent;
    deficiency: StudentDeficiencyShowRecord;
    can: { update: boolean; delete: boolean };
}): React.JSX.Element {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [failure, setFailure] = useState("");
    const itemUrl = `${studentsBase}/${student.id}/deficiencies/${deficiency.id}`;
    const profileName = deficiency.deficiency?.name ?? "Perfil não identificado";

    useEffect(() => {
        if (!failure) return;

        notify({
            tone: "error",
            title: "Não foi possível concluir",
            text: failure,
        });
    }, [failure]);

    function remove(): void {
        if (deleting) return;

        setFailure("");
        setDeleting(true);
        router.delete(itemUrl, {
            preserveScroll: true,
            onHttpException: () => {
                setFailure("Não foi possível excluir o perfil de atendimento. Tente novamente.");
                return false;
            },
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <StudentDeficiencyPage
            title="Detalhes do Perfil de Atendimento do Aluno"
            subtitle={`Aluno: ${student.person.name}`}
            student={student}
            studentDeficiency={deficiency}
        >
            <CardBox className="studentDeficiencyShow__panel">
                <FormSection
                    id="student-deficiency-diagnosis"
                    title="Informações do Diagnóstico"
                    variant="inline"
                    iconClass="fa fa-stethoscope"
                >
                    <dl className="studentDeficiencyShow__grid">
                        <DetailField label="Nome do Perfil">
                            {profileName}
                        </DetailField>
                        <DetailField label="Severidade">
                            {deficiency.severity_label ? (
                                <TableLabel tone={deficiency.severity_color ?? "inverse"}>
                                    {deficiency.severity_label}
                                </TableLabel>
                            ) : "Não informada"}
                        </DetailField>
                        <DetailField label="Data do Registro">
                            {dateTimeLabel(deficiency.created_at)}
                        </DetailField>
                        <DetailField
                            label="Observações Técnicas"
                            wide
                        >
                            <span className="studentDeficiencyShow__text">
                                {deficiency.notes || "Nenhuma observação registrada."}
                            </span>
                        </DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions>
                <StudentRecordReturnButton
                    studentId={student.id}
                    tab="support-profiles"
                />
                {can.update ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar perfil de atendimento"
                        aria-label="Editar perfil de atendimento"
                    />
                ) : null}
                {can.delete ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir perfil de atendimento"
                        aria-label="Excluir perfil de atendimento"
                        disabled={deleting}
                        onClick={() => {
                            setFailure("");
                            setDeleteOpen(true);
                        }}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={deleteOpen}
                title="Excluir Perfil de Atendimento"
                message={(
                    <>
                        Deseja excluir permanentemente o perfil{" "}
                        <strong>{profileName}</strong>{" "}
                        do registro do aluno?
                    </>
                )}
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo perfil..."
                confirmTone="dark-red"
                onClose={() => {
                    if (deleting) return;
                    setDeleteOpen(false);
                    setFailure("");
                }}
                onConfirm={remove}
            />
        </StudentDeficiencyPage>
    );
}
