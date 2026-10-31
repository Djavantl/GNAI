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
import DisciplinePage, { disciplineBase } from "./DisciplinePage";
import type { DisciplineFilters, DisciplinePaginator, DisciplineRecord } from "./Discipline.types";
import "./Disciplines.css";

type Props = {
    disciplines: DisciplinePaginator;
    filters: Partial<DisciplineFilters>;
    can: { create: boolean; view: boolean; delete: boolean };
};

const emptyFilters: DisciplineFilters = { name: "", is_active: "" };
const statusOptions = [
    { value: "1", label: "Ativo" },
    { value: "0", label: "Inativo" },
];
const columns: TableListColumn[] = [
    { id: "name", label: "Nome", width: "60%", minWidth: 140, fixed: "start" },
    { id: "status", label: "Status", width: "25%", minWidth: 90 },
    { id: "actions", label: "Ações", width: "15%", minWidth: 86, fixed: "end" },
];

export default function SpecializedEducationalSupportDisciplinesIndexPage({
    disciplines,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const pending = useRef(false);
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [failure, setFailure] = useState("");
    const [disciplineToDelete, setDisciplineToDelete] = useState<DisciplineRecord | null>(null);
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

    function visit(filters: DisciplineFilters, page = 1): void {
        if (pending.current) return;
        pending.current = true;
        setBusy(true);
        setListLoading(true);
        setFailure("");
        router.get(disciplineBase, { ...compactListFilters(filters), page }, {
            preserveState: true,
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível atualizar as disciplinas. Tente novamente.");
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
        if (pending.current || !disciplineToDelete) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.delete(`${disciplineBase}/${disciplineToDelete.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir a disciplina. Tente novamente.");
                return false;
            },
            onSuccess: () => setDisciplineToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    return (
        <DisciplinePage
            title="Disciplinas"
            subtitle="Gerencie as disciplinas cadastradas no sistema."
            actions={can.create ? (
                <TableListCreateButton
                    href={`${disciplineBase}/create`}
                    ariaLabel="Cadastrar disciplina"
                />
            ) : undefined}
        >
            <TableListCard
                aria-label="Listagem de disciplinas"
                aria-busy={busy}
                loading={listLoading}
                loadingLabel="Atualizando disciplinas"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="disciplineIndex__filters">
                        <FormField
                            id="discipline-filter-name"
                            label="Nome da Disciplina"
                            error={pageErrors.name}
                        >
                            <TextInput
                                id="discipline-filter-name"
                                name="name"
                                value={values.name}
                                maxLength={255}
                                placeholder="Nome da disciplina..."
                                disabled={busy}
                                invalid={!!pageErrors.name}
                                aria-describedby={pageErrors.name ? "discipline-filter-name-error" : undefined}
                                onChange={(event) => update("name", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="discipline-filter-status"
                            label="Status"
                            error={pageErrors.is_active}
                        >
                            <SelectField
                                id="discipline-filter-status"
                                name="is_active"
                                label="Status"
                                placeholder="Todos"
                                value={values.is_active}
                                options={statusOptions}
                                disabled={busy}
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "discipline-filter-status-error" : undefined}
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
                <TableListScroll aria-label="Tabela de disciplinas">
                    <TableListTable
                        className="disciplineIndex__table"
                        columns={columns}
                        page={disciplines}
                    >
                        {disciplines.data.length ? disciplines.data.map((discipline) => (
                            <tr key={discipline.id}>
                                <td>
                                    <span className="disciplineIndex__name">{discipline.name}</span>
                                </td>
                                <td>
                                    <TableLabel tone={discipline.is_active ? "success" : "inverse"}>
                                        {discipline.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
                                <td>
                                    <TableActions>
                                        {can.view ? (
                                            <Button
                                                href={`${disciplineBase}/${discipline.id}/show`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                aria-label={`Visualizar ${discipline.name}`}
                                            />
                                        ) : null}
                                        {can.delete ? (
                                            <Button
                                                tone="dark-red"
                                                size="sm"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                aria-label={`Excluir ${discipline.name}`}
                                                disabled={busy}
                                                onClick={() => {
                                                    cancelScheduled();
                                                    setFailure("");
                                                    setDisciplineToDelete(discipline);
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
                                    Nenhuma disciplina encontrada.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={disciplines}
                    disabled={busy}
                    paginationLabel="Paginação de disciplinas"
                    onSelect={(page) => visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={disciplineToDelete !== null}
                title="Excluir Disciplina"
                message={(
                    <>
                        Deseja excluir a disciplina <strong>{disciplineToDelete?.name}</strong>?
                    </>
                )}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo disciplina..."
                confirmTone="dark-red"
                onClose={() => {
                    if (busy) return;
                    setDisciplineToDelete(null);
                    setFailure("");
                }}
                onConfirm={remove}
            />
        </DisciplinePage>
    );
}
