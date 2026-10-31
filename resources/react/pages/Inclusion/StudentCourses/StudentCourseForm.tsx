import { useRef } from "react";
import { useForm } from "@inertiajs/react";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import ChoiceGroup from "@/components/Forms/ChoiceGroup/ChoiceGroup";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import { aeeBase, studentRecordUrl } from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import type {
    StudentCourseCourse,
    StudentCourseDiscipline,
    StudentCourseRecord,
    StudentCourseStudent,
} from "./StudentCourse.types";
import StudentCoursePage from "./StudentCoursePage";
import "./StudentCourses.css";

type DisciplineGroupName = "failed_discipline_ids" | "at_risk_discipline_ids";

type StudentCourseFormData = {
    course_id: string;
    academic_year: string;
    is_current: boolean;
    school_attendance_status: string;
    failed_discipline_ids: number[];
    at_risk_discipline_ids: number[];
};

const disciplineGroups: { name: DisciplineGroupName; title: string; iconClass: string }[] = [
    {
        name: "failed_discipline_ids",
        title: "Disciplinas com Reprovação",
        iconClass: "fa fa-times-circle",
    },
    {
        name: "at_risk_discipline_ids",
        title: "Disciplinas com Risco de Insucesso Acadêmico",
        iconClass: "fa fa-exclamation-triangle",
    },
];

type StudentCourseFormProps =
    | {
        mode: "create";
        student: StudentCourseStudent;
        courses: StudentCourseCourse[];
        mustBeCurrent: boolean;
    }
    | {
        mode: "edit";
        studentCourse: StudentCourseRecord;
    };

export default function StudentCourseForm(props: StudentCourseFormProps): React.JSX.Element {
    const editing = props.mode === "edit";
    const studentCourse = props.mode === "edit" ? props.studentCourse : undefined;
    const student = props.mode === "edit" ? props.studentCourse.student : props.student;
    const studentId = props.mode === "edit"
        ? props.studentCourse.student?.id ?? props.studentCourse.student_id
        : props.student.id;
    const mustBeCurrent = props.mode === "create" && props.mustBeCurrent;
    const courses = props.mode === "create" ? props.courses : [];

    const form = useForm<StudentCourseFormData>({
        course_id: studentCourse ? String(studentCourse.course?.id ?? studentCourse.course_id) : "",
        academic_year: studentCourse
            ? String(studentCourse.academic_year ?? "")
            : String(new Date().getFullYear()),
        is_current: mustBeCurrent,
        school_attendance_status: studentCourse?.school_attendance_status ?? "",
        failed_discipline_ids: (studentCourse?.failed_disciplines ?? []).map((item) => Number(item.id)),
        at_risk_discipline_ids: (studentCourse?.at_risk_disciplines ?? []).map((item) => Number(item.id)),
    });
    const errors = form.errors as Record<string, string | undefined>;
    const busy = useRef(false);
    const element = useRef<HTMLFormElement>(null);
    const cancelUrl = studentRecordUrl(studentId, "academic-data");

    const isCurrent = mustBeCurrent || form.data.is_current;
    const showFollowUp = editing || isCurrent;
    const selectedCourse = studentCourse
        ? studentCourse.course
        : courses.find((course) => String(course.id) === form.data.course_id);
    const disciplines: StudentCourseDiscipline[] = [...(selectedCourse?.disciplines ?? [])]
        .sort((first, second) => first.name.localeCompare(second.name, "pt-BR"));
    const courseOptions = courses.map((course) => ({
        value: String(course.id),
        label: course.name,
    }));

    const groupError = (name: DisciplineGroupName): string | undefined => errors[name]
        ?? Object.entries(errors).find(([key]) => key.startsWith(`${name}.`))?.[1];

    function toggleDiscipline(name: DisciplineGroupName, id: number, checked: boolean): void {
        form.setData((current) => ({
            ...current,
            [name]: checked
                ? [...current[name].filter((item) => item !== id), id]
                : current[name].filter((item) => item !== id),
        }));
    }

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing) return;
        busy.current = true;

        const allowed = new Set(disciplines.map((discipline) => Number(discipline.id)));

        form.transform((data) => {
            // Legacy form only submitted the checkboxes of the selected course while it was current.
            const failed = showFollowUp
                ? data.failed_discipline_ids.filter((id) => allowed.has(id))
                : [];
            const atRisk = showFollowUp
                ? data.at_risk_discipline_ids.filter((id) => allowed.has(id))
                : [];
            const payload = {
                course_id: data.course_id,
                academic_year: data.academic_year,
                school_attendance_status: data.school_attendance_status,
                failed_discipline_ids: failed,
                at_risk_discipline_ids: atRisk,
            };

            return editing
                ? payload
                : { ...payload, is_current: mustBeCurrent || data.is_current };
        });

        const options = {
            preserveScroll: true,
            onError: () => requestAnimationFrame(() => {
                element.current
                    ?.querySelector<HTMLElement>('[aria-invalid="true"]')
                    ?.focus();
            }),
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
                    text: editing
                        ? "Não foi possível salvar a matrícula. Tente novamente."
                        : "Não foi possível cadastrar a matrícula. Tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (studentCourse) {
            form.put(`${aeeBase}/student-courses/${studentCourse.id}`, options);
        } else {
            form.post(`${aeeBase}/student-courses/store/${studentId}`, options);
        }
    }

    return (
        <StudentCoursePage
            title={editing ? "Editar Matrícula" : "Nova Matrícula"}
            subtitle={editing
                ? "Atualize as informações acadêmicas desta matrícula."
                : "Vincule o aluno a um curso ou série."}
            studentId={studentId}
            studentName={student?.person?.name ?? ""}
            studentCourse={studentCourse
                ? { id: studentCourse.id, courseName: studentCourse.course?.name ?? "" }
                : undefined}
        >
            <FormLayout
                ref={element}
                onSubmit={submit}
                aria-busy={form.processing}
            >
                <CardBox>
                    <fieldset disabled={form.processing}>
                        <FormSection
                            id="student-course-student"
                            title={editing ? "Dados do Aluno e Curso" : "Aluno"}
                            variant="inline"
                            iconClass="fa fa-user"
                        >
                            <dl className="studentCourseForm__details">
                                <DetailField label={editing ? "Aluno" : "Nome do Aluno"}>
                                    <strong>{student?.person?.name ?? "Não informado"}</strong>
                                </DetailField>
                                {editing ? (
                                    <DetailField label="Curso / Série">
                                        <strong>{studentCourse?.course?.name ?? "Não informado"}</strong>
                                    </DetailField>
                                ) : (
                                    <DetailField label="Matrícula">
                                        {student?.registration || "Não informado"}
                                    </DetailField>
                                )}
                            </dl>
                        </FormSection>

                        <FormSection
                            id="student-course-enrollment"
                            title="Dados da Matrícula"
                            variant="inline"
                            iconClass="fa fa-graduation-cap"
                        >
                            <FormGrid columns={2}>
                                {editing ? null : (
                                    <FormField
                                        id="student-course-course"
                                        label="Curso / Série"
                                        requiredIndicator
                                        error={errors.course_id}
                                    >
                                        <SelectField
                                            id="student-course-course"
                                            name="course_id"
                                            label="Selecionar curso ou série"
                                            required
                                            options={courseOptions}
                                            value={form.data.course_id}
                                            disabled={form.processing}
                                            aria-invalid={!!errors.course_id}
                                            aria-describedby={errors.course_id ? "student-course-course-error" : undefined}
                                            onChange={(event) => form.setData("course_id", event.target.value)}
                                        />
                                    </FormField>
                                )}
                                <FormField
                                    id="student-course-academic-year"
                                    label="Ano Letivo"
                                    requiredIndicator
                                    error={errors.academic_year}
                                >
                                    <TextInput
                                        id="student-course-academic-year"
                                        name="academic_year"
                                        type="number"
                                        required
                                        inputMode="numeric"
                                        value={form.data.academic_year}
                                        invalid={!!errors.academic_year}
                                        aria-describedby={errors.academic_year ? "student-course-academic-year-error" : undefined}
                                        onChange={(event) => form.setData("academic_year", event.target.value)}
                                    />
                                </FormField>
                                {editing ? null : (
                                    <FormGridWide>
                                        <div className="studentCourseForm__toggle">
                                            <ToggleControl
                                                id="student-course-is-current"
                                                name="is_current"
                                                size="small"
                                                label="Definir este como o curso atual do aluno"
                                                description={mustBeCurrent
                                                    ? "Este será o primeiro curso do aluno e precisa ser definido como atual."
                                                    : "Ao marcar, o curso atual anterior será movido para o histórico."}
                                                checked={isCurrent}
                                                disabled={mustBeCurrent || form.processing}
                                                onChange={(checked) => {
                                                    if (!mustBeCurrent) form.setData("is_current", checked);
                                                }}
                                            />
                                            {errors.is_current ? (
                                                <span id="student-course-is-current-error" className="formField__error">
                                                    {errors.is_current}
                                                </span>
                                            ) : null}
                                        </div>
                                    </FormGridWide>
                                )}
                            </FormGrid>
                        </FormSection>

                        {showFollowUp ? (
                            <FormSection
                                id="student-course-follow-up"
                                title="Acompanhamento Acadêmico do Curso Atual"
                                variant="inline"
                                iconClass="fa fa-line-chart"
                            >
                                <FormGrid columns={2}>
                                    <FormGridWide>
                                        <FormField
                                            id="student-course-attendance"
                                            label="Situação da Frequência Escolar"
                                            error={errors.school_attendance_status}
                                        >
                                            <TextArea
                                                id="student-course-attendance"
                                                name="school_attendance_status"
                                                rows={3}
                                                placeholder="Descreva como está a frequência escolar do aluno neste curso..."
                                                value={form.data.school_attendance_status}
                                                aria-invalid={!!errors.school_attendance_status}
                                                aria-describedby={errors.school_attendance_status
                                                    ? "student-course-attendance-error"
                                                    : undefined}
                                                onChange={(event) => form.setData("school_attendance_status", event.target.value)}
                                            />
                                        </FormField>
                                    </FormGridWide>
                                    {selectedCourse ? disciplineGroups.map((group) => {
                                        const groupId = `student-course-${group.name.replace(/_/g, "-")}`;
                                        const error = groupError(group.name);

                                        return (
                                            <div
                                                key={group.name}
                                                className="studentCourseForm__disciplines"
                                            >
                                                <h3
                                                    id={`${groupId}-title`}
                                                    className="studentCourseForm__disciplinesTitle"
                                                >
                                                    <i className={group.iconClass} aria-hidden="true" />
                                                    <span>{group.title}</span>
                                                </h3>
                                                {disciplines.length > 0 ? (
                                                    <ChoiceGroup
                                                        id={groupId}
                                                        label={group.title}
                                                        invalid={!!error}
                                                        describedBy={error ? `${groupId}-error` : undefined}
                                                    >
                                                        {disciplines.map((discipline) => (
                                                            <ChoiceControl
                                                                key={discipline.id}
                                                                type="checkbox"
                                                                id={`${group.name}_${selectedCourse.id}_${discipline.id}`}
                                                                name={`${group.name}[]`}
                                                                label={discipline.name}
                                                                checked={form.data[group.name].includes(Number(discipline.id))}
                                                                disabled={form.processing}
                                                                onChange={(checked) => toggleDiscipline(group.name, Number(discipline.id), checked)}
                                                            />
                                                        ))}
                                                    </ChoiceGroup>
                                                ) : (
                                                    <p className="studentCourseForm__empty">
                                                        Nenhuma disciplina vinculada ao curso.
                                                    </p>
                                                )}
                                                {error ? (
                                                    <span id={`${groupId}-error`} className="formField__error">
                                                        {error}
                                                    </span>
                                                ) : null}
                                            </div>
                                        );
                                    }) : null}
                                </FormGrid>
                            </FormSection>
                        ) : null}
                    </fieldset>
                </CardBox>
                <FormActions
                    status={form.processing
                        ? (editing ? "Salvando alterações da matrícula..." : "Efetivando matrícula do aluno...")
                        : undefined}
                >
                    <Button
                        href={cancelUrl}
                        tone="cancel"
                        label="Cancelar"
                        title={editing ? "Cancelar edição da matrícula" : "Cancelar criação de matrícula"}
                        aria-label={editing ? "Cancelar edição da matrícula" : "Cancelar criação de matrícula"}
                        disabled={form.processing}
                    />
                    <Button
                        type="submit"
                        tone="save"
                        leftIconClass="fa fa-check"
                        label="Salvar"
                        title={editing ? "Salvar alterações da matrícula" : "Efetivar matrícula do aluno"}
                        aria-label={editing ? "Salvar alterações da matrícula" : "Efetivar matrícula do aluno"}
                        disabled={form.processing}
                    />
                </FormActions>
            </FormLayout>
        </StudentCoursePage>
    );
}
