import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import SemesterPage, { semesterBase, semesterLabel } from "./SemesterPage";
import type { SemesterRecord } from "./Semester.types";
import "./Semesters.css";

const dayInMilliseconds = 24 * 60 * 60 * 1000;

/** Reads the calendar date serialized by Laravel ("Y-m-d" or ISO) without timezone shifts. */
function dateParts(value: string | null): [number, number, number] | null {
    const match = value?.match(/^(\d{4})-(\d{2})-(\d{2})/);

    return match ? [Number(match[1]), Number(match[2]), Number(match[3])] : null;
}

function formatDate(value: string | null): string {
    const parts = dateParts(value);
    if (!parts) return "Não definida";
    const [year, month, day] = parts;

    return `${String(day).padStart(2, "0")}/${String(month).padStart(2, "0")}/${year}`;
}

function durationInDays(start: string | null, end: string | null): number | null {
    const startParts = dateParts(start);
    const endParts = dateParts(end);
    if (!startParts || !endParts) return null;

    const startTime = Date.UTC(startParts[0], startParts[1] - 1, startParts[2]);
    const endTime = Date.UTC(endParts[0], endParts[1] - 1, endParts[2]);

    return Math.round((endTime - startTime) / dayInMilliseconds);
}

export default function InclusionSemestersShowPage({
    semester,
    can,
}: {
    semester: SemesterRecord;
    can: { update: boolean; delete: boolean };
}): React.JSX.Element {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [failure, setFailure] = useState("");
    const itemUrl = `${semesterBase}/${semester.id}`;
    const duration = durationInDays(semester.start_date, semester.end_date);
    // As in the legacy view, the current semester cannot be deleted from here.
    const canDelete = can.delete && !semester.is_current;

    useEffect(() => {
        if (failure) {
            notify({ tone: "error", title: "Não foi possível concluir", text: failure });
        }
    }, [failure]);

    function remove(): void {
        if (deleting) return;
        setFailure("");
        setDeleting(true);
        router.delete(itemUrl, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir o semestre. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <SemesterPage
            title="Detalhes do Semestre"
            subtitle="Configuração do período letivo e vigência dos atendimentos."
            semester={semester}
        >
            <CardBox aria-busy={deleting}>
                <FormSection
                    id="semester-show-identification"
                    title="Identificação do Período"
                    variant="inline"
                    iconClass="fa fa-calendar"
                >
                    <dl className="semesterShow__grid">
                        <DetailField label="Rótulo / Identificador">
                            <strong className="semesterShow__label">
                                {semesterLabel(semester)}
                            </strong>
                        </DetailField>
                        <DetailField label="Ano Letivo">
                            {semester.year}
                        </DetailField>
                        <DetailField label="Etapa (Termo)">
                            {semester.term}º Semestre
                        </DetailField>
                        <DetailField label="Vigência Atual">
                            <TableLabel tone={semester.is_current ? "success" : "inverse"}>
                                {semester.is_current
                                    ? "Semestre ativo no sistema"
                                    : "Período encerrado / histórico"}
                            </TableLabel>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="semester-show-duration"
                    title="Duração do Semestre"
                    variant="inline"
                    iconClass="fa fa-clock-o"
                >
                    <dl className="semesterShow__grid">
                        <DetailField label="Data de Início">
                            {formatDate(semester.start_date)}
                        </DetailField>
                        <DetailField label="Data de Término">
                            {formatDate(semester.end_date)}
                        </DetailField>
                        <DetailField label="Duração Total">
                            {duration === null
                                ? "---"
                                : `${duration} dias letivos aproximados`}
                        </DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions>
                <Button
                    href={semesterBase}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para semestres"
                    aria-label="Voltar para semestres"
                    disabled={deleting}
                />
                {can.update ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar semestre"
                        aria-label="Editar semestre"
                        disabled={deleting}
                    />
                ) : null}
                {canDelete ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir semestre"
                        aria-label="Excluir semestre"
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
                title="Excluir Semestre"
                message={
                    <>
                        Excluir o semestre <strong>{semesterLabel(semester)}</strong> pode afetar os registros de frequência e sessões vinculados a este período.
                    </>
                }
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo semestre..."
                confirmTone="dark-red"
                onClose={() => {
                    if (deleting) return;
                    setFailure("");
                    setDeleteOpen(false);
                }}
                onConfirm={remove}
            />
        </SemesterPage>
    );
}
