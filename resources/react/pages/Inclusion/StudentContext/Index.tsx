import { usePage } from "@inertiajs/react";
import { useMemo } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import StudentRecordReturnButton from "@/components/Students/StudentRecordReturnButton";
import { aeeBase, dateLabel } from "@/components/Students/Students.utils";
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
import StudentContextPage from "./StudentContextPage";
import type {
    StudentContextFilters,
    StudentContextListRecord,
    StudentContextPaginator,
} from "./StudentContext.types";
import "./StudentContext.css";

type Props = {
    contexts: StudentContextPaginator;
    student: { id: number; person: { name: string } };
    hasContexts: boolean;
    semesters: { value: string; label: string }[];
    filters: Partial<StudentContextFilters>;
    can: { create: boolean; view: boolean; delete: boolean };
};

const emptyFilters: StudentContextFilters = {
    semester_id: "",
    evaluation_type: "",
    is_current: "",
};
const evaluationTypeOptions = [
    { value: "initial", label: "Inicial" },
    { value: "periodic_review", label: "Revisão Periódica" },
];
const statusOptions = [
    { value: "1", label: "Atual" },
    { value: "0", label: "Antigo" },
];
const columns: TableListColumn[] = [
    { id: "date", label: "Data", width: "18%", minWidth: 90, fixed: "start" },
    { id: "version", label: "Versão", width: "14%", minWidth: 72 },
    { id: "evaluation", label: "Tipo de avaliação", width: "30%", minWidth: 120 },
    { id: "status", label: "Status", width: "16%", minWidth: 80 },
    { id: "semester", label: "Semestre", width: "16%", defaultVisible: false },
    { id: "evaluator", label: "Avaliador", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "22%", minWidth: 104, fixed: "end" },
];

export default function InclusionStudentContextIndexPage({
    contexts,
    student,
    hasContexts,
    semesters,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const listUrl = `${aeeBase}/student-context/${student.id}/index`;
    const list = useTableListVisit({
        url: listUrl,
        failureText: "Não foi possível atualizar o histórico de contextos. Tente novamente.",
    });
    const removal = useTableListAction<StudentContextListRecord>({
        failureText: "Não foi possível excluir o contexto do aluno. Tente novamente.",
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
        <StudentContextPage
            index
            title="Histórico de Contextos"
            subtitle={`Veja o histórico completo das características comportamentais observadas do aluno(a) ${student.person.name}.`}
            studentId={student.id}
            studentName={student.person.name}
            actions={(
                <>
                    <StudentRecordReturnButton
                        studentId={student.id}
                        tab="current-context"
                    />
                    {can.create ? (
                        <TableListCreateButton
                            href={hasContexts
                                ? `${aeeBase}/student-context/${student.id}/new-version`
                                : `${aeeBase}/student-context/${student.id}/create`}
                            ariaLabel={hasContexts
                                ? "Cadastrar nova versão do contexto"
                                : "Cadastrar contexto do aluno"}
                        />
                    ) : null}
                </>
            )}
        >
            <TableListCard
                aria-label="Histórico de contextos do aluno"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando histórico de contextos"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="studentContextIndex__filters">
                        <FormField
                            id="student-context-filter-semester"
                            label="Semestre"
                            error={pageErrors.semester_id}
                        >
                            <SelectField
                                id="student-context-filter-semester"
                                name="semester_id"
                                label="Semestre"
                                placeholder="Todos"
                                value={values.semester_id}
                                options={semesters}
                                aria-invalid={!!pageErrors.semester_id}
                                aria-describedby={pageErrors.semester_id ? "student-context-filter-semester-error" : undefined}
                                onChange={(event) => update("semester_id", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="student-context-filter-type"
                            label="Tipo"
                            error={pageErrors.evaluation_type}
                        >
                            <SelectField
                                id="student-context-filter-type"
                                name="evaluation_type"
                                label="Tipo"
                                placeholder="Todos"
                                value={values.evaluation_type}
                                options={evaluationTypeOptions}
                                aria-invalid={!!pageErrors.evaluation_type}
                                aria-describedby={pageErrors.evaluation_type ? "student-context-filter-type-error" : undefined}
                                onChange={(event) => update("evaluation_type", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="student-context-filter-status"
                            label="Status"
                            error={pageErrors.is_current}
                        >
                            <SelectField
                                id="student-context-filter-status"
                                name="is_current"
                                label="Status"
                                placeholder="Todos"
                                value={values.is_current}
                                options={statusOptions}
                                aria-invalid={!!pageErrors.is_current}
                                aria-describedby={pageErrors.is_current ? "student-context-filter-status-error" : undefined}
                                onChange={(event) => update("is_current", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela do histórico de contextos">
                    <TableListTable
                        columns={columns}
                        page={contexts}
                    >
                        {contexts.data.length ? contexts.data.map((context) => {
                            const canDelete = can.delete && context.can_manage;

                            return (
                                <tr key={context.id}>
                                    <td>
                                        <strong>{dateLabel(context.created_at)}</strong>
                                    </td>
                                    <td>V{context.version}</td>
                                    <td>{context.evaluation_type_label}</td>
                                    <td>
                                        <TableLabel tone={context.is_current ? "success" : "inverse"}>
                                            {context.is_current ? "Atual" : "Histórico"}
                                        </TableLabel>
                                    </td>
                                    <td>{context.semester?.label || "Não informado"}</td>
                                    <td>{context.evaluator?.person?.name || "Não informado"}</td>
                                    <td>
                                        {can.view || canDelete ? (
                                            <TableActions>
                                                {can.view ? (
                                                    <Button
                                                        href={`${aeeBase}/student-context/${context.id}/show`}
                                                        size="sm"
                                                        tone="dark-blue"
                                                        iconOnly
                                                        leftIconClass="fa fa-eye"
                                                        aria-label={`Visualizar contexto versão ${context.version}`}
                                                    />
                                                ) : null}
                                                {canDelete ? (
                                                    <Button
                                                        tone="dark-red"
                                                        size="sm"
                                                        iconOnly
                                                        leftIconClass="fa fa-trash-o"
                                                        aria-label={`Excluir contexto versão ${context.version}`}
                                                        disabled={removal.processing}
                                                        onClick={() => {
                                                            cancelScheduled();
                                                            removal.open(context);
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
                                    Nenhum contexto do aluno encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={contexts}
                    disabled={list.loading}
                    paginationLabel="Paginação do histórico de contextos"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Contexto do Aluno"
                message={(
                    <>
                        Excluir permanentemente o contexto{" "}
                        <strong>V{removal.target?.version}</strong>?
                    </>
                )}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo contexto..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (!removal.target) return;
                    cancelScheduled();
                    list.cancel();
                    removal.submit("delete", `${aeeBase}/student-context/${removal.target.id}`);
                }}
            />
        </StudentContextPage>
    );
}
