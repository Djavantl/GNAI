import { usePage } from "@inertiajs/react";
import { useMemo } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    formatTableListText,
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
import CoursePage, { courseBase } from "./CoursePage";
import type { CourseFilters, CourseListRecord, CoursePaginator } from "./Course.types";
import "./Courses.css";

type Props = {
    courses: CoursePaginator;
    filters: Partial<CourseFilters>;
    can: { create: boolean; view: boolean; delete: boolean };
};

const emptyFilters: CourseFilters = { name: "", is_active: "" };
const statusOptions = [
    { value: "1", label: "Ativo" },
    { value: "0", label: "Inativo" },
];
const columns: TableListColumn[] = [
    { id: "name", label: "Nome do Curso", width: "45%", minWidth: 140, fixed: "start" },
    { id: "disciplines", label: "Disciplinas", width: "22%", minWidth: 100 },
    { id: "status", label: "Status", width: "18%", minWidth: 80 },
    { id: "description", label: "Descrição", width: "16%", defaultVisible: false },
    { id: "enrollments", label: "Vínculos de alunos", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "15%", minWidth: 86, fixed: "end" },
];

export default function InclusionCoursesIndexPage({
    courses,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: courseBase,
        failureText: "Não foi possível atualizar os cursos. Tente novamente.",
    });
    const removal = useTableListAction<CourseListRecord>({
        failureText: "Não foi possível excluir o curso. Tente novamente.",
    });
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
        <CoursePage
            title="Cursos e Séries"
            subtitle="Gerencie cursos e suas respectivas séries."
            actions={can.create ? (
                <TableListCreateButton
                    href={`${courseBase}/create`}
                    ariaLabel="Cadastrar curso"
                />
            ) : undefined}
        >
            <TableListCard
                aria-label="Listagem de cursos"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando cursos"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="courseIndex__filters">
                        <FormField
                            id="course-filter-name"
                            label="Nome do Curso"
                            error={pageErrors.name}
                        >
                            <TextInput
                                id="course-filter-name"
                                name="name"
                                value={values.name}
                                maxLength={255}
                                placeholder="Buscar por nome do curso..."
                                invalid={!!pageErrors.name}
                                aria-describedby={pageErrors.name ? "course-filter-name-error" : undefined}
                                onChange={(event) => update("name", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="course-filter-status"
                            label="Status"
                            error={pageErrors.is_active}
                        >
                            <SelectField
                                id="course-filter-status"
                                name="is_active"
                                label="Status"
                                placeholder="Todos"
                                value={values.is_active}
                                options={statusOptions}
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "course-filter-status-error" : undefined}
                                onChange={(event) => update("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de cursos">
                    <TableListTable
                        className="courseIndex__table"
                        columns={columns}
                        page={courses}
                    >
                        {courses.data.length ? courses.data.map((course) => (
                            <tr key={course.id}>
                                <td>{course.name}</td>
                                <td>{course.disciplines_count} matérias</td>
                                <td>
                                    <TableLabel tone={course.is_active ? "success" : "inverse"}>
                                        {course.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
                                <td>{formatTableListText(course.description)}</td>
                                <td>{course.student_courses_count}</td>
                                <td>
                                    <TableActions>
                                        {can.view ? (
                                            <Button
                                                href={`${courseBase}/${course.id}/show`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                aria-label={`Visualizar ${course.name}`}
                                            />
                                        ) : null}
                                        {can.delete ? (
                                            <Button
                                                tone="dark-red"
                                                size="sm"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                aria-label={`Excluir ${course.name}`}
                                                disabled={removal.processing}
                                                onClick={() => {
                                                    cancelScheduled();
                                                    removal.open(course);
                                                }}
                                            />
                                        ) : null}
                                        {!can.view && !can.delete ? <span>Sem ações</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    Nenhum curso encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={courses}
                    disabled={list.loading}
                    paginationLabel="Paginação de cursos"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Curso"
                message={(
                    <>
                        Excluir o curso <strong>{removal.target?.name}</strong>?
                    </>
                )}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo curso..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (removal.target) removal.submit("delete", `${courseBase}/${removal.target.id}`);
                }}
            />
        </CoursePage>
    );
}
