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
    { id: "description", label: "Descrição", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "22%", minWidth: 120, fixed: "end" },
];

export default function InclusionPositionsIndexPage({
    positions,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: positionsBase,
        failureText: "Não foi possível atualizar os cargos. Tente novamente.",
    });
    const removal = useTableListAction<PositionListRecord>({
        failureText: "Não foi possível excluir o cargo. Tente novamente.",
    });
    const statusChange = useTableListAction<PositionListRecord>({
        failureText: "Não foi possível alterar o status do cargo. Tente novamente.",
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
    const hasRowActions = can.view || can.update || can.delete;

    function toggleActive(position: PositionListRecord): void {
        cancelScheduled();
        statusChange.submit("patch", `${positionsBase}/${position.id}/deactivate`);
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
                aria-busy={list.loading}
                loading={list.loading}
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
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "position-filter-status-error" : undefined}
                                onChange={(event) => update("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
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
                                <td>{formatTableListText(position.description)}</td>
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
                                                disabled={statusChange.processing}
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
                                                disabled={removal.processing}
                                                onClick={() => {
                                                    cancelScheduled();
                                                    removal.open(position);
                                                }}
                                            />
                                        ) : null}
                                        {!hasRowActions ? <span>Sem ações</span> : null}
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
                    disabled={list.loading}
                    paginationLabel="Paginação de cargos"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Cargo"
                message={(
                    <>
                        Deseja excluir o cargo <strong>{removal.target?.name}</strong>?
                    </>
                )}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo cargo..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (removal.target) removal.submit("delete", `${positionsBase}/${removal.target.id}`);
                }}
            />
        </PositionPage>
    );
}
