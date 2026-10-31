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
import PositionPage, { positionsBase } from "./PositionPage";
import type { PositionFilters, PositionListRecord, PositionPaginator } from "./Position.types";

type Props = {
    positions: PositionPaginator;
    filters: Partial<PositionFilters>;
    can: { create: boolean; view: boolean; update: boolean; delete: boolean };
};

const emptyFilters: PositionFilters = { name: "", description: "", is_active: "" };
const statusOptions = [
    { value: "1", label: "Ativo" },
    { value: "0", label: "Inativo" },
];
const columns: TableListColumn[] = [
    { id: "name", label: "Cargo", width: "58%", minWidth: 140, fixed: "start" },
    { id: "status", label: "Status", width: "20%", minWidth: 90 },
    { id: "actions", label: "Ações", width: "22%", minWidth: 120, fixed: "end" },
];
const connectionFailure = "Falha de conexão. Confira sua conexão e tente novamente.";

export default function SpecializedEducationalSupportPositionsIndexPage({
    positions,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const pending = useRef(false);
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [failure, setFailure] = useState("");
    const [positionToDelete, setPositionToDelete] = useState<PositionListRecord | null>(null);
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters }),
        [initialFilters],
    );
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (filters) => visit(filters),
    });
    const hasRowActions = can.view || can.update || can.delete;

    useEffect(() => {
        if (failure) {
            notify({ tone: "error", title: "Não foi possível concluir", text: failure });
        }
    }, [failure]);

    function visit(filters: PositionFilters, page = 1): void {
        if (pending.current) return;
        pending.current = true;
        setBusy(true);
        setListLoading(true);
        setFailure("");
        router.get(positionsBase, { ...compactListFilters(filters), page }, {
            preserveState: true,
            preserveScroll: true,
            onNetworkError: () => {
                setFailure(connectionFailure);
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível atualizar os cargos. Tente novamente.");
                return false;
            },
            onFinish: () => {
                pending.current = false;
                setBusy(false);
                setListLoading(false);
            },
        });
    }

    function toggleActive(position: PositionListRecord): void {
        if (pending.current) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.patch(`${positionsBase}/${position.id}/deactivate`, {}, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure(connectionFailure);
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível alterar o status do cargo. Tente novamente.");
                return false;
            },
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    function remove(): void {
        if (pending.current || !positionToDelete) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.delete(`${positionsBase}/${positionToDelete.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure(connectionFailure);
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir o cargo. Tente novamente.");
                return false;
            },
            onSuccess: () => setPositionToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    return (
        <PositionPage
            title="Lista de Cargos"
            subtitle="Gerenciamento de funções para o suporte especializado."
            actions={can.create ? (
                <TableListCreateButton
                    href={`${positionsBase}/create`}
                    ariaLabel="Cadastrar cargo"
                />
            ) : undefined}
        >
            <TableListCard
                aria-label="Listagem de cargos"
                aria-busy={busy}
                loading={listLoading}
                loadingLabel="Atualizando cargos"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="positionIndex__filters">
                        <FormField
                            id="position-filter-name"
                            label="Nome"
                            error={pageErrors.name}
                        >
                            <TextInput
                                id="position-filter-name"
                                name="name"
                                value={values.name}
                                maxLength={255}
                                placeholder="Buscar por nome..."
                                disabled={busy}
                                invalid={!!pageErrors.name}
                                aria-describedby={pageErrors.name ? "position-filter-name-error" : undefined}
                                onChange={(event) => update("name", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="position-filter-description"
                            label="Descrição"
                            error={pageErrors.description}
                        >
                            <TextInput
                                id="position-filter-description"
                                name="description"
                                value={values.description}
                                placeholder="Descrição..."
                                disabled={busy}
                                invalid={!!pageErrors.description}
                                aria-describedby={pageErrors.description ? "position-filter-description-error" : undefined}
                                onChange={(event) => update("description", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="position-filter-status"
                            label="Status"
                            error={pageErrors.is_active}
                        >
                            <SelectField
                                id="position-filter-status"
                                name="is_active"
                                label="Status"
                                placeholder="Todos"
                                value={values.is_active}
                                options={statusOptions}
                                disabled={busy}
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "position-filter-status-error" : undefined}
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
                <TableListScroll aria-label="Tabela de cargos">
                    <TableListTable
                        className="positionIndex__table"
                        columns={columns}
                        page={positions}
                    >
                        {positions.data.length ? positions.data.map((position) => (
                            <tr key={position.id}>
                                <td>
                                    <strong>{position.name}</strong>
                                </td>
                                <td>
                                    <TableLabel tone={position.is_active ? "success" : "inverse"}>
                                        {position.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
                                <td>
                                    <TableActions>
                                        {can.view ? (
                                            <Button
                                                href={`${positionsBase}/${position.id}/show`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                aria-label={`Visualizar ${position.name}`}
                                            />
                                        ) : null}
                                        {can.update ? (
                                            <Button
                                                size="sm"
                                                tone="inverse"
                                                iconOnly
                                                leftIconClass={position.is_active ? "fa fa-toggle-on" : "fa fa-toggle-off"}
                                                aria-label={`${position.is_active ? "Desativar" : "Ativar"} ${position.name}`}
                                                disabled={busy}
                                                onClick={() => toggleActive(position)}
                                            />
                                        ) : null}
                                        {can.delete ? (
                                            <Button
                                                tone="dark-red"
                                                size="sm"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                aria-label={`Excluir ${position.name}`}
                                                disabled={busy}
                                                onClick={() => {
                                                    cancelScheduled();
                                                    setFailure("");
                                                    setPositionToDelete(position);
                                                }}
                                            />
                                        ) : null}
                                        {!hasRowActions ? <span>—</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    Nenhum cargo encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={positions}
                    disabled={busy}
                    paginationLabel="Paginação de cargos"
                    onSelect={(page) => visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={positionToDelete !== null}
                title="Excluir Cargo"
                message={(
                    <>
                        Deseja excluir o cargo <strong>{positionToDelete?.name}</strong>?
                    </>
                )}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo cargo..."
                confirmTone="dark-red"
                onClose={() => {
                    if (busy) return;
                    setPositionToDelete(null);
                    setFailure("");
                }}
                onConfirm={remove}
            />
        </PositionPage>
    );
}
