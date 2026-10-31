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
import DisciplinePage, { disciplineBase } from "./DisciplinePage";
import type { DisciplineDetailRecord } from "./Discipline.types";
import "./Disciplines.css";

export default function InclusionDisciplinesShowPage({
    discipline,
    can,
}: {
    discipline: DisciplineDetailRecord;
    can: { update: boolean; delete: boolean };
}): React.JSX.Element {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [failure, setFailure] = useState("");
    const courses = discipline.courses ?? [];
    const itemUrl = `${disciplineBase}/${discipline.id}`;

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
                setFailure("Não foi possível excluir a disciplina. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <DisciplinePage
            title="Detalhes da Disciplina"
            subtitle="Informações cadastrais e descrição da matéria."
            discipline={discipline}
        >
            <CardBox aria-busy={deleting}>
                <FormSection
                    id="discipline-show-general"
                    title="Informações Gerais"
                    variant="inline"
                    iconClass="fa fa-book"
                >
                    <dl className="disciplineShow__grid">
                        <DetailField label="Nome da Disciplina">
                            <strong>{discipline.name}</strong>
                        </DetailField>
                        <DetailField label="Status">
                            <TableLabel tone={discipline.is_active ? "success" : "danger"}>
                                {discipline.is_active ? "Ativo" : "Inativo"}
                            </TableLabel>
                        </DetailField>
                        <DetailField
                            label="Descrição / Objetivos"
                            wide
                        >
                            <span className="disciplineShow__text">
                                {discipline.description || "Nenhuma descrição informada."}
                            </span>
                        </DetailField>
                        <DetailField
                            label="Cursos vinculados"
                            wide
                        >
                            {courses.length ? (
                                <ul className="disciplineShow__list">
                                    {courses.map((course) => (
                                        <li key={course.id}>{course.name}</li>
                                    ))}
                                </ul>
                            ) : (
                                "Nenhum curso vinculado."
                            )}
                        </DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions>
                <Button
                    href={disciplineBase}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para disciplinas"
                    aria-label="Voltar para disciplinas"
                    disabled={deleting}
                />
                {can.update ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar disciplina"
                        aria-label="Editar disciplina"
                        disabled={deleting}
                    />
                ) : null}
                {can.delete ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir disciplina"
                        aria-label="Excluir disciplina"
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
                title="Excluir Disciplina"
                message={
                    <>
                        Deseja realmente excluir a disciplina <strong>{discipline.name}</strong>?
                    </>
                }
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo disciplina..."
                confirmTone="dark-red"
                onClose={() => {
                    if (deleting) return;
                    setFailure("");
                    setDeleteOpen(false);
                }}
                onConfirm={remove}
            />
        </DisciplinePage>
    );
}
