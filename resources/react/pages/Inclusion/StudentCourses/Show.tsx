import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import StudentRecordReturnButton from "@/components/Students/StudentRecordReturnButton";
import { aeeBase, dateTimeLabel } from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import StudentCoursePage from "./StudentCoursePage";
import type {
    StudentCourseDiscipline,
    StudentCourseShowRecord,
} from "./StudentCourse.types";
import "./StudentCourses.css";

type PendingAction = "make-current" | "delete" | null;

function DisciplineLabels({
    items,
    tone,
}: {
    items: StudentCourseDiscipline[];
    tone: string;
}): React.JSX.Element {
    if (!items.length) {
        return <span className="studentCourseShow__muted">Nenhuma.</span>;
    }

    return (
        <ul className="studentCourseShow__labels">
            {items.map((discipline) => (
                <li key={discipline.id}>
                    <TableLabel tone={tone}>
                        {discipline.name}
                    </TableLabel>
                </li>
            ))}
        </ul>
    );
}

export default function InclusionStudentCoursesShowPage({
    studentCourse,
    can,
}: {
    studentCourse: StudentCourseShowRecord;
    can: { update: boolean; delete: boolean };
}): React.JSX.Element {
    const [action, setAction] = useState<PendingAction>(null);
    const [processing, setProcessing] = useState(false);
    const [failure, setFailure] = useState("");
    const studentId = studentCourse.student?.id ?? studentCourse.student_id;
    const studentName = studentCourse.student?.person?.name ?? "";
    const courseName = studentCourse.course?.name ?? "Curso não identificado";
    const itemUrl = `${aeeBase}/student-courses/${studentCourse.id}`;
    const curriculum = [...(studentCourse.course?.disciplines ?? [])].sort(
        (first, second) => first.name.localeCompare(second.name, "pt-BR"),
    );

    useEffect(() => {
        if (!failure) return;

        notify({
            tone: "error",
            title: "Não foi possível concluir",
            text: failure,
        });
    }, [failure]);

    function confirm(): void {
        if (processing || !action) return;

        const options = {
            preserveScroll: true,
            onHttpException: () => {
                setFailure(
                    action === "delete"
                        ? "Não foi possível excluir a matrícula. Tente novamente."
                        : "Não foi possível tornar este o curso atual. Tente novamente.",
                );
                return false;
            },
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onSuccess: () => setAction(null),
            onFinish: () => setProcessing(false),
        };

        setFailure("");
        setProcessing(true);

        if (action === "delete") {
            router.delete(itemUrl, options);
        } else {
            router.patch(`${itemUrl}/make-current`, {}, options);
        }
    }

    return (
        <StudentCoursePage
            title={`Matrícula, ${studentName}`}
            subtitle="Detalhes completos da matrícula do aluno."
            studentId={studentId}
            studentName={studentName}
            studentCourse={{ id: studentCourse.id, courseName }}
            current={null}
        >
            <CardBox className="studentCourseShow__panel">
                <FormSection
                    id="student-course-data"
                    title="Dados da Matrícula"
                    variant="inline"
                    iconClass="fa fa-graduation-cap"
                >
                    <dl className="studentCourseShow__grid">
                        <DetailField label="Aluno">
                            <strong>{studentName || "---"}</strong>
                        </DetailField>
                        <DetailField label="Curso / Série">
                            {courseName}
                        </DetailField>
                        <DetailField label="Ano Letivo">
                            {studentCourse.academic_year ?? "---"}
                        </DetailField>
                        <DetailField label="Situação">
                            <TableLabel tone={studentCourse.is_current ? "success" : "inverse"}>
                                {studentCourse.is_current ? "Atual" : "Histórico"}
                            </TableLabel>
                        </DetailField>
                        <DetailField
                            label="Descrição do Curso"
                            wide
                        >
                            <span className="studentCourseShow__text">
                                {studentCourse.course?.description || "Não informado"}
                            </span>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="student-course-follow-up"
                    title="Acompanhamento Acadêmico no Curso"
                    variant="inline"
                    iconClass="fa fa-line-chart"
                >
                    <dl className="studentCourseShow__grid">
                        <DetailField
                            label="Situação da Frequência Escolar"
                            wide
                        >
                            <span className="studentCourseShow__text">
                                {studentCourse.school_attendance_status || "Não informada."}
                            </span>
                        </DetailField>
                        <DetailField label="Disciplinas com Reprovação">
                            <DisciplineLabels
                                items={studentCourse.failed_disciplines ?? []}
                                tone="danger"
                            />
                        </DetailField>
                        <DetailField label="Disciplinas com Risco de Insucesso Acadêmico">
                            <DisciplineLabels
                                items={studentCourse.at_risk_disciplines ?? []}
                                tone="warning"
                            />
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="student-course-curriculum"
                    title="Matriz Curricular"
                    variant="inline"
                    iconClass="fa fa-book"
                >
                    {curriculum.length ? (
                        <ul
                            className="studentCourseShow__curriculum"
                            aria-label="Lista de disciplinas do curso"
                        >
                            {curriculum.map((discipline) => (
                                <li
                                    key={discipline.id}
                                    title={discipline.description ?? undefined}
                                >
                                    {discipline.name}
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <p className="studentCourseShow__muted">
                            Nenhuma disciplina vinculada ao curso.
                        </p>
                    )}
                </FormSection>
                <FormSection
                    id="student-course-system"
                    title="Informações do Sistema"
                    variant="inline"
                    iconClass="fa fa-info-circle"
                >
                    <dl className="studentCourseShow__grid">
                        <DetailField label="Criado em">
                            {dateTimeLabel(studentCourse.created_at)}
                        </DetailField>
                        <DetailField label="Atualizado em">
                            {dateTimeLabel(studentCourse.updated_at)}
                        </DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions>
                <StudentRecordReturnButton
                    studentId={studentId}
                    tab="academic-data"
                />
                {can.update && studentCourse.is_current ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar matrícula"
                        aria-label="Editar matrícula"
                    />
                ) : null}
                {can.update && !studentCourse.is_current ? (
                    <Button
                        tone="success"
                        leftIconClass="fa fa-check-circle"
                        label="Tornar Atual"
                        title={`Tornar ${courseName} o curso atual`}
                        aria-label={`Tornar ${courseName} o curso atual`}
                        disabled={processing}
                        onClick={() => {
                            setFailure("");
                            setAction("make-current");
                        }}
                    />
                ) : null}
                {can.delete ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir matrícula"
                        aria-label="Excluir matrícula"
                        disabled={processing}
                        onClick={() => {
                            setFailure("");
                            setAction("delete");
                        }}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={action === "make-current"}
                title="Tornar Curso Atual"
                message="Deseja tornar este o curso atual do aluno? O curso atual será movido para o histórico."
                processing={processing}
                confirmLabel="Tornar atual"
                processingLabel="Atualizando matrícula..."
                confirmTone="success"
                onClose={() => {
                    if (processing) return;
                    setAction(null);
                    setFailure("");
                }}
                onConfirm={confirm}
            />
            <ConfirmActionModal
                open={action === "delete"}
                title="Excluir Matrícula"
                message={(
                    <>
                        Deseja excluir a matrícula no curso{" "}
                        <strong>{courseName}</strong>?
                    </>
                )}
                processing={processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo matrícula..."
                confirmTone="dark-red"
                onClose={() => {
                    if (processing) return;
                    setAction(null);
                    setFailure("");
                }}
                onConfirm={confirm}
            />
        </StudentCoursePage>
    );
}
