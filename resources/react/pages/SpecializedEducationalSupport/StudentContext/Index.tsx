import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useRef, useState } from "react";
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
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters, { compactListFilters } from "@/hooks/useAutoListFilters";
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
    { id: "actions", label: "Ações", width: "22%", minWidth: 104, fixed: "end" },
];

export default function SpecializedEducationalSupportStudentContextIndexPage({
    contexts,
    student,
    hasContexts,
    semesters,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const pending = useRef(false);
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [failure, setFailure] = useState("");
    const [contextToDelete, setContextToDelete] = useState<StudentContextListRecord | null>(null);
    const listUrl = `${aeeBase}/student-context/${student.id}/index`;
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

    function visit(filters: StudentContextFilters, page = 1): void {
        if (pending.current) return;
        pending.current = true;
        setBusy(true);
        setListLoading(true);
        setFailure("");
        router.get(listUrl, { ...compactListFilters(filters), page }, {
            preserveState: true,
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível atualizar o histórico de contextos. Tente novamente.");
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
        if (pending.current || !contextToDelete) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.delete(`${aeeBase}/student-context/${contextToDelete.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir o contexto do aluno. Tente novamente.");
                return false;
            },
            onSuccess: () => setContextToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

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
                aria-busy={busy}
                loading={listLoading}
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
                                disabled={busy}
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
                                disabled={busy}
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
                                disabled={busy}
                                aria-invalid={!!pageErrors.is_current}
                                aria-describedby={pageErrors.is_current ? "student-context-filter-status-error" : undefined}
                                onChange={(event) => update("is_current", event.target.value)}
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
                                                        disabled={busy}
                                                        onClick={() => {
                                                            cancelScheduled();
                                                            setFailure("");
                                                            setContextToDelete(context);
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
                    disabled={busy}
                    paginationLabel="Paginação do histórico de contextos"
                    onSelect={(page) => visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={contextToDelete !== null}
                title="Excluir Contexto do Aluno"
                message={(
                    <>
                        Excluir permanentemente o contexto{" "}
                        <strong>V{contextToDelete?.version}</strong>?
                    </>
                )}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo contexto..."
                confirmTone="dark-red"
                onClose={() => {
                    if (busy) return;
                    setContextToDelete(null);
                    setFailure("");
                }}
                onConfirm={remove}
            />
        </StudentContextPage>
    );
}
