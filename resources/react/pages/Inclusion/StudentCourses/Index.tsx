import { usePage } from "@inertiajs/react";
import { useMemo } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import StudentRecordReturnButton from "@/components/Students/StudentRecordReturnButton";
import { aeeBase } from "@/components/Students/Students.utils";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    TableListCard,
    TableListClearButton,
    TableListCreateButton,
    TableListEmpty,
    TableListFilterActions,
    TableListFooter,
    TableListScroll,
    TableListTable,
    useTableListAction,
    useTableListVisit,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import StudentCoursePage from "./StudentCoursePage";
import type {
    StudentCourseFilters,
    StudentCourseListRecord,
    StudentCoursePaginator,
} from "./StudentCourse.types";
import "./StudentCourses.css";

type Props = {
    student: { id: number; person: { name: string } };
    studentCourses: StudentCoursePaginator;
    courses: { value: string; label: string }[];
    filters: Partial<StudentCourseFilters>;
    can: { create: boolean; view: boolean; delete: boolean };
};

const emptyFilters: StudentCourseFilters = {
    course_id: "",
    academic_year: "",
    is_current: "",
};
const statusOptions = [
    { value: "1", label: "Curso Atual" },
    { value: "0", label: "Histórico" },
];
const columns: TableListColumn[] = [
    { id: "student", label: "Aluno", width: "26%", minWidth: 120, fixed: "start" },
    { id: "course", label: "Curso / Série", width: "28%", minWidth: 130 },
    { id: "year", label: "Ano letivo", width: "14%", minWidth: 90 },
    { id: "status", label: "Situação", width: "14%", minWidth: 90 },
    { id: "actions", label: "Ações", width: "18%", minWidth: 104, fixed: "end" },
];

export default function InclusionStudentCoursesIndexPage({
    student,
    studentCourses,
    courses,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const listUrl = `${aeeBase}/students/${student.id}/history`;
    const list = useTableListVisit({
        url: listUrl,
        failureText: "Não foi possível atualizar as matrículas. Tente novamente.",
    });
    const removal = useTableListAction<StudentCourseListRecord>({
        failureText: "Não foi possível excluir a matrícula. Tente novamente.",
    });
    const studentName = student.person.name;
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters }),
        [initialFilters],
    );
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (filters) => list.visit(filters),
    });

    return (
        <StudentCoursePage
            index
            title={`Matrículas, ${studentName}`}
            subtitle="Gerencie as matrículas e o histórico acadêmico do aluno."
            studentId={student.id}
            studentName={studentName}
            actions={(
                <>
                    <StudentRecordReturnButton
                        studentId={student.id}
                        tab="academic-data"
                    />
                    {can.create ? (
                        <TableListCreateButton
                            href={`${aeeBase}/student-courses/${student.id}/create`}
                            ariaLabel="Cadastrar nova matrícula"
                        />
                    ) : null}
                </>
            )}
        >
            <TableListCard
                aria-label="Listagem de matrículas do aluno"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando matrículas"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="studentCourseIndex__filters">
                        <FormField
                            id="student-course-filter-course"
                            label="Curso (histórico)"
                            error={pageErrors.course_id}
                        >
                            <SelectField
                                id="student-course-filter-course"
                                name="course_id"
                                label="Curso (histórico)"
                                placeholder="Todos"
                                value={values.course_id}
                                options={courses}
                                aria-invalid={!!pageErrors.course_id}
                                aria-describedby={pageErrors.course_id ? "student-course-filter-course-error" : undefined}
                                onChange={(event) => update("course_id", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="student-course-filter-year"
                            label="Ano letivo"
                            error={pageErrors.academic_year}
                        >
                            <TextInput
                                id="student-course-filter-year"
                                name="academic_year"
                                value={values.academic_year}
                                inputMode="numeric"
                                maxLength={4}
                                placeholder="Ano letivo"
                                invalid={!!pageErrors.academic_year}
                                aria-describedby={pageErrors.academic_year ? "student-course-filter-year-error" : undefined}
                                onChange={(event) => update("academic_year", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="student-course-filter-status"
                            label="Status"
                            error={pageErrors.is_current}
                        >
                            <SelectField
                                id="student-course-filter-status"
                                name="is_current"
                                label="Status"
                                placeholder="Todos"
                                value={values.is_current}
                                options={statusOptions}
                                aria-invalid={!!pageErrors.is_current}
                                aria-describedby={pageErrors.is_current ? "student-course-filter-status-error" : undefined}
                                onChange={(event) => update("is_current", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de matrículas do aluno">
                    <TableListTable
                        columns={columns}
                        page={studentCourses}
                    >
                        {studentCourses.data.length ? studentCourses.data.map((enrollment) => {
                            const courseName = enrollment.course?.name ?? "Curso não identificado";

                            return (
                                <tr key={enrollment.id}>
                                    <td>{studentName}</td>
                                    <td>{courseName}</td>
                                    <td>{enrollment.academic_year}</td>
                                    <td>
                                        <TableLabel tone={enrollment.is_current ? "success" : "inverse"}>
                                            {enrollment.is_current ? "Atual" : "Histórico"}
                                        </TableLabel>
                                    </td>
                                    <td>
                                        {can.view || can.delete ? (
                                            <TableActions>
                                                {can.view ? (
                                                    <Button
                                                        href={`${aeeBase}/student-courses/${enrollment.id}`}
                                                        size="sm"
                                                        tone="dark-blue"
                                                        iconOnly
                                                        leftIconClass="fa fa-eye"
                                                        aria-label={`Visualizar curso ${courseName} de ${studentName}`}
                                                    />
                                                ) : null}
                                                {can.delete ? (
                                                    <Button
                                                        tone="dark-red"
                                                        size="sm"
                                                        iconOnly
                                                        leftIconClass="fa fa-trash-o"
                                                        aria-label={`Excluir matrícula no curso ${courseName}`}
                                                        disabled={removal.processing}
                                                        onClick={() => {
                                                            cancelScheduled();
                                                            removal.open(enrollment);
                                                        }}
                                                    />
                                                ) : null}
                                            </TableActions>
                                        ) : "Nenhuma ação"}
                                    </td>
                                </tr>
                            );
                        }) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    Nenhum curso do aluno encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={studentCourses}
                    disabled={list.loading}
                    paginationLabel="Paginação de matrículas"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Matrícula"
                message={(
                    <>
                        Excluir a matrícula no curso{" "}
                        <strong>{removal.target?.course?.name ?? "não identificado"}</strong>{" "}
                        do histórico do aluno?
                    </>
                )}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo matrícula..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (!removal.target) return;
                    cancelScheduled();
                    list.cancel();
                    removal.submit("delete", `${aeeBase}/student-courses/${removal.target.id}`);
                }}
            />
        </StudentCoursePage>
    );
}
