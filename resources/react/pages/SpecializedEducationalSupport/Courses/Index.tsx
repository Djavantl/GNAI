import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useRef, useState } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
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
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters, { compactListFilters } from "@/hooks/useAutoListFilters";
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
    { id: "actions", label: "Ações", width: "15%", minWidth: 86, fixed: "end" },
];

export default function SpecializedEducationalSupportCoursesIndexPage({
    courses,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const pending = useRef(false);
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [failure, setFailure] = useState("");
    const [courseToDelete, setCourseToDelete] = useState<CourseListRecord | null>(null);
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters }),
        [initialFilters],
    );
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (filters) => visit(filters),
    });

    useEffect(() => {
        if (failure) {
            notify({ tone: "error", title: "Não foi possível concluir", text: failure });
        }
    }, [failure]);

    function visit(filters: CourseFilters, page = 1): void {
        if (pending.current) return;
        pending.current = true;
        setBusy(true);
        setListLoading(true);
        setFailure("");
        router.get(courseBase, { ...compactListFilters(filters), page }, {
            preserveState: true,
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível atualizar os cursos. Tente novamente.");
                return false;
            },
            onFinish: () => {
                pending.current = false;
                setBusy(false);
                setListLoading(false);
            },
        });
    }

    function remove(): void {
        if (pending.current || !courseToDelete) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.delete(`${courseBase}/${courseToDelete.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir o curso. Tente novamente.");
                return false;
            },
            onSuccess: () => setCourseToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

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
                aria-busy={busy}
                loading={listLoading}
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
                                disabled={busy}
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
                                disabled={busy}
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "course-filter-status-error" : undefined}
                                onChange={(event) => update("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton
                                disabled={busy}
                                onClick={clear}
                            />
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
                                                disabled={busy}
                                                onClick={() => {
                                                    cancelScheduled();
                                                    setFailure("");
                                                    setCourseToDelete(course);
                                                }}
                                            />
                                        ) : null}
                                        {!can.view && !can.delete ? <span>—</span> : null}
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
                    disabled={busy}
                    paginationLabel="Paginação de cursos"
                    onSelect={(page) => visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={courseToDelete !== null}
                title="Excluir Curso"
                message={(
                    <>
                        Excluir o curso <strong>{courseToDelete?.name}</strong>?
                    </>
                )}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo curso..."
                confirmTone="dark-red"
                onClose={() => {
                    if (busy) return;
                    setCourseToDelete(null);
                    setFailure("");
                }}
                onConfirm={remove}
            />
        </CoursePage>
    );
}
