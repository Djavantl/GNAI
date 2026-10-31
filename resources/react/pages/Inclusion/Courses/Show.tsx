import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import {
    TableListEmpty,
    TableListScroll,
    TableListTable,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import CoursePage, { courseBase } from "./CoursePage";
import type { CourseRecord } from "./Course.types";
import "./Courses.css";

const disciplineColumns: TableListColumn[] = [
    { id: "name", label: "Disciplina", width: "75%", minWidth: 160, fixed: "start" },
    { id: "type", label: "Tipo", width: "25%", minWidth: 110, fixed: "end" },
];

export default function InclusionCoursesShowPage({
    course,
    can,
}: {
    course: CourseRecord;
    can: { update: boolean; delete: boolean };
}): React.JSX.Element {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [failure, setFailure] = useState("");
    const disciplines = course.disciplines ?? [];
    const itemUrl = `${courseBase}/${course.id}`;

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
                setFailure("Não foi possível excluir o curso. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <CoursePage
            title="Detalhes do Curso"
            subtitle="Informações estruturais e grade de disciplinas."
            course={course}
        >
            <CardBox aria-busy={deleting}>
                <FormSection
                    id="course-show-general"
                    title="Informações Gerais"
                    variant="inline"
                    iconClass="fa fa-graduation-cap"
                >
                    <dl className="courseShow__grid">
                        <DetailField label="Nome do Curso">
                            <strong>{course.name}</strong>
                        </DetailField>
                        <DetailField label="Status">
                            <TableLabel tone={course.is_active ? "success" : "danger"}>
                                {course.is_active ? "Ativo" : "Inativo"}
                            </TableLabel>
                        </DetailField>
                        <DetailField
                            label="Descrição / Observações"
                            wide
                        >
                            <span className="courseShow__text">
                                {course.description || "Nenhuma descrição informada."}
                            </span>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="course-show-curriculum"
                    title="Matriz Curricular (Disciplinas)"
                    variant="inline"
                    iconClass="fa fa-book"
                >
                    <TableListScroll aria-label="Disciplinas da matriz curricular">
                        <TableListTable
                            columns={disciplineColumns}
                            summary={`${disciplines.length} ${disciplines.length === 1 ? "disciplina" : "disciplinas"}`}
                        >
                            {disciplines.length ? (
                                disciplines.map((discipline) => (
                                    <tr key={discipline.id}>
                                        <td>{discipline.name}</td>
                                        <td>
                                            <TableLabel tone="purple">Obrigatória</TableLabel>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <TableListEmpty colSpan={disciplineColumns.length}>
                                        Nenhuma disciplina vinculada a este curso.
                                    </TableListEmpty>
                                </tr>
                            )}
                        </TableListTable>
                    </TableListScroll>
                </FormSection>
            </CardBox>
            <FormActions>
                <Button
                    href={courseBase}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para cursos"
                    aria-label="Voltar para cursos"
                    disabled={deleting}
                />
                {can.update ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar curso"
                        aria-label="Editar curso"
                        disabled={deleting}
                    />
                ) : null}
                {can.delete ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir curso"
                        aria-label="Excluir curso"
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
                title="Excluir Curso"
                message={
                    <>
                        Excluir o curso <strong>{course.name}</strong>?
                    </>
                }
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo curso..."
                confirmTone="dark-red"
                onClose={() => {
                    if (deleting) return;
                    setFailure("");
                    setDeleteOpen(false);
                }}
                onConfirm={remove}
            />
        </CoursePage>
    );
}
