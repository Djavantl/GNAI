import { useMemo } from "react";
import { usePage } from "@inertiajs/react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    formatTableListDate,
    formatTableListText,
    TableListCard,
    TableListClearButton,
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
import { dateLabel } from "@/components/Students/Students.utils";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import type {
    PendencyListFilters,
    PendencyListProps,
    PendencyListRecord,
    PendencyListVariant,
} from "./PendencyList.types";
import "./PendencyList.css";

export const pendenciesBase = "/specialized-educational-support/pendencies";
export const myPendenciesUrl = "/specialized-educational-support/my-pendencies";

const emptyFilters: PendencyListFilters = {
    title: "",
    assigned_to: "",
    priority: "",
    is_completed: "",
};

const priorityTones: Record<string, string> = {
    info: "info",
    warning: "warning",
    danger: "danger",
    dark: "inverse",
};

const allColumns: TableListColumn[] = [
    { id: "title", label: "Título", width: "30%", minWidth: 120, fixed: "start" },
    { id: "professional", label: "Profissional", width: "22%", minWidth: 100 },
    { id: "priority", label: "Prioridade", width: "13%", minWidth: 80 },
    { id: "due_date", label: "Vencimento", width: "12%", minWidth: 80 },
    { id: "completed", label: "Concluída", width: "11%", minWidth: 80 },
    { id: "creator", label: "Criado por", width: "16%", defaultVisible: false },
    { id: "description", label: "Descrição", width: "16%", defaultVisible: false },
    { id: "created", label: "Criado em", width: "16%", defaultVisible: false },
    { id: "updated", label: "Atualizado em", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "12%", minWidth: 86, fixed: "end" },
];

const myColumns: TableListColumn[] = [
    { id: "title", label: "Título", width: "40%", minWidth: 120, fixed: "start" },
    { id: "priority", label: "Prioridade", width: "16%", minWidth: 80 },
    { id: "due_date", label: "Vencimento", width: "15%", minWidth: 80 },
    { id: "status", label: "Status", width: "17%", minWidth: 90 },
    { id: "creator", label: "Criado por", width: "16%", defaultVisible: false },
    { id: "description", label: "Descrição", width: "16%", defaultVisible: false },
    { id: "created", label: "Criado em", width: "16%", defaultVisible: false },
    { id: "updated", label: "Atualizado em", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "12%", minWidth: 70, fixed: "end" },
];

const statusOptions = [
    { value: "0", label: "Pendentes" },
    { value: "1", label: "Concluídas" },
];

export default function PendencyList({
    variant,
    pendencies,
    priorities,
    professionals = [],
    filters: initialFilters,
    can,
}: PendencyListProps & {
    variant: PendencyListVariant;
}): React.JSX.Element {
    const isMine = variant === "my";
    const listUrl = isMine ? myPendenciesUrl : pendenciesBase;
    const columns = isMine ? myColumns : allColumns;
    const canView = can.view;
    const canDelete = !isMine && Boolean(can.delete);
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: listUrl,
        failureText: "Não foi possível atualizar as pendências. Tente novamente.",
    });
    const removal = useTableListAction<PendencyListRecord>({
        failureText: "Não foi possível excluir a pendência. Tente novamente.",
    });
    const priorityOptions = useMemo(
        () => priorities.map(({ value, label }) => ({ value, label })),
        [priorities],
    );
    const priorityByValue = useMemo(
        () => new Map(priorities.map((priority) => [priority.value, priority])),
        [priorities],
    );
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters }),
        [initialFilters],
    );
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (next) => list.visit(isMine ? { ...next, assigned_to: "" } : next),
    });

    function describedBy(name: keyof PendencyListFilters, id: string): string | undefined {
        return pageErrors[name] ? `${id}-error` : undefined;
    }

    function renderPriority(pendency: PendencyListRecord): React.JSX.Element | string {
        const priority = pendency.priority ? priorityByValue.get(pendency.priority) : undefined;

        if (!priority) return "Não informado";

        return (
            <TableLabel tone={priorityTones[priority.color] ?? "inverse"}>
                {priority.label}
            </TableLabel>
        );
    }

    function renderStatus(pendency: PendencyListRecord): React.JSX.Element {
        if (isMine) {
            return (
                <TableLabel tone={pendency.is_completed ? "success" : "danger"}>
                    <i
                        className={pendency.is_completed ? "fa fa-check-circle" : "fa fa-clock-o"}
                        aria-hidden="true"
                    />
                    {" "}
                    {pendency.is_completed ? "Concluída" : "Pendente"}
                </TableLabel>
            );
        }

        return (
            <TableLabel tone={pendency.is_completed ? "success" : "danger"}>
                <i
                    className={pendency.is_completed ? "fa fa-check-circle" : "fa fa-times-circle"}
                    aria-hidden="true"
                />
                {" "}
                {pendency.is_completed ? "Sim" : "Não"}
            </TableLabel>
        );
    }

    return (
        <>
            <TableListCard
                aria-label={isMine ? "Listagem das minhas pendências" : "Listagem de pendências"}
                loading={list.loading}
                loadingLabel="Atualizando pendências"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div
                        className={
                            isMine
                                ? "pendencyList__filters pendencyList__filters--my"
                                : "pendencyList__filters"
                        }
                    >
                        <FormField
                            id="pendency-filter-title"
                            label="Título"
                            error={pageErrors.title}
                        >
                            <TextInput
                                id="pendency-filter-title"
                                name="title"
                                value={values.title}
                                maxLength={255}
                                placeholder="Buscar por título..."
                                invalid={Boolean(pageErrors.title)}
                                aria-describedby={describedBy("title", "pendency-filter-title")}
                                onChange={(event) => update("title", event.target.value)}
                            />
                        </FormField>
                        {!isMine ? (
                            <FormField
                                id="pendency-filter-professional"
                                label="Profissional"
                                error={pageErrors.assigned_to}
                            >
                                <SelectField
                                    id="pendency-filter-professional"
                                    name="assigned_to"
                                    label="Profissional"
                                    placeholder="Todos"
                                    value={values.assigned_to}
                                    options={professionals}
                                    aria-invalid={Boolean(pageErrors.assigned_to)}
                                    aria-describedby={describedBy("assigned_to", "pendency-filter-professional")}
                                    onChange={(event) => update("assigned_to", event.target.value)}
                                />
                            </FormField>
                        ) : null}
                        <FormField
                            id="pendency-filter-priority"
                            label="Prioridade"
                            error={pageErrors.priority}
                        >
                            <SelectField
                                id="pendency-filter-priority"
                                name="priority"
                                label="Prioridade"
                                placeholder="Todas"
                                value={values.priority}
                                options={priorityOptions}
                                aria-invalid={Boolean(pageErrors.priority)}
                                aria-describedby={describedBy("priority", "pendency-filter-priority")}
                                onChange={(event) => update("priority", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="pendency-filter-status"
                            label="Status"
                            error={pageErrors.is_completed}
                        >
                            <SelectField
                                id="pendency-filter-status"
                                name="is_completed"
                                label="Status"
                                placeholder="Todos"
                                value={values.is_completed}
                                options={statusOptions}
                                aria-invalid={Boolean(pageErrors.is_completed)}
                                aria-describedby={describedBy("is_completed", "pendency-filter-status")}
                                onChange={(event) => update("is_completed", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton
                                onClick={clear}
                            />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de pendências">
                    <TableListTable
                        columns={columns}
                        page={pendencies}
                    >
                        {pendencies.data.length ? (
                            pendencies.data.map((pendency) => (
                                <tr key={pendency.id}>
                                    <td>
                                        {isMine ? <strong>{pendency.title}</strong> : pendency.title}
                                    </td>
                                    {!isMine ? (
                                        <td>{pendency.assigned_professional?.person?.name || "Não informado"}</td>
                                    ) : null}
                                    <td>{renderPriority(pendency)}</td>
                                    <td>{pendency.due_date ? dateLabel(pendency.due_date) : "Não informado"}</td>
                                    <td>{renderStatus(pendency)}</td>
                                    <td>{pendency.creator?.name || "Não informado"}</td>
                                    <td>{formatTableListText(pendency.description)}</td>
                                    <td>{formatTableListDate(pendency.created_at, true)}</td>
                                    <td>{formatTableListDate(pendency.updated_at, true)}</td>
                                    <td>
                                        <TableActions>
                                            {canView ? (
                                                <Button
                                                    href={`${pendenciesBase}/${pendency.id}/show`}
                                                    size="sm"
                                                    tone="dark-blue"
                                                    iconOnly
                                                    leftIconClass="fa fa-eye"
                                                    aria-label={`Visualizar pendência ${pendency.title}`}
                                                />
                                            ) : null}
                                            {canDelete ? (
                                                <Button
                                                    tone="dark-red"
                                                    size="sm"
                                                    iconOnly
                                                    leftIconClass="fa fa-trash-o"
                                                    aria-label={`Excluir pendência ${pendency.title}`}
                                                    disabled={removal.processing}
                                                    onClick={() => {
                                                        cancelScheduled();
                                                        removal.open(pendency);
                                                    }}
                                                />
                                            ) : null}
                                            {!canView && !canDelete ? (
                                                <span>Nenhuma ação</span>
                                            ) : null}
                                        </TableActions>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    {isMine
                                        ? "Nenhuma pendência encontrada para você."
                                        : "Nenhuma pendência encontrada."}
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={pendencies}
                    disabled={list.loading}
                    paginationLabel="Paginação de pendências"
                    onSelect={(page) => list.visit(isMine ? { ...values, assigned_to: "" } : values, page)}
                />
            </TableListCard>
            {canDelete ? (
                <ConfirmActionModal
                    open={removal.target !== null}
                    title="Excluir Pendência"
                    message={
                        <>
                            Deseja excluir a pendência <strong>{removal.target?.title}</strong>?
                        </>
                    }
                    processing={removal.processing}
                    confirmLabel="Confirmar exclusão"
                    processingLabel="Excluindo pendência..."
                    confirmTone="dark-red"
                    onClose={removal.close}
                    onConfirm={() => {
                        if (!removal.target) return;
                        cancelScheduled();
                        removal.submit("delete", `${pendenciesBase}/${removal.target.id}`);
                    }}
                />
            ) : null}
        </>
    );
}
