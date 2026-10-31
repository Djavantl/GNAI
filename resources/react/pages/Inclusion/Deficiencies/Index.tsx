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
import DeficiencyPage, { deficiencyBase } from "./DeficiencyPage";
import type { DeficiencyFilters, DeficiencyPaginator, DeficiencyRecord } from "./Deficiency.types";
import "./Deficiencies.css";

type Props = {
    deficiencies: DeficiencyPaginator;
    filters: Partial<DeficiencyFilters>;
    can: { create: boolean; view: boolean; update: boolean; delete: boolean };
};

const emptyFilters: DeficiencyFilters = { name: "", cid_code: "", is_active: "" };
const statusOptions = [
    { value: "1", label: "Ativo" },
    { value: "0", label: "Inativo" },
];
const columns: TableListColumn[] = [
    { id: "name", label: "Nome / CID", width: "58%", minWidth: 160, fixed: "start" },
    { id: "status", label: "Status", width: "20%", minWidth: 90 },
    { id: "cid", label: "Código CID", width: "16%", defaultVisible: false },
    { id: "description", label: "Descrição", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "22%", minWidth: 120, fixed: "end" },
];

export default function InclusionDeficienciesIndexPage({
    deficiencies,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: deficiencyBase,
        failureText: "Não foi possível atualizar os perfis de atendimento. Tente novamente.",
    });
    const removal = useTableListAction<DeficiencyRecord>({
        failureText: "Não foi possível excluir o perfil de atendimento. Tente novamente.",
    });
    const statusChange = useTableListAction<DeficiencyRecord>({
        failureText: "Não foi possível alterar o status do perfil de atendimento. Tente novamente.",
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

    function toggleActive(deficiency: DeficiencyRecord): void {
        cancelScheduled();
        statusChange.submit("patch", `${deficiencyBase}/${deficiency.id}/deactivate`);
    }

    return (
        <DeficiencyPage
            title="Lista de Perfis de Atendimento"
            subtitle="Cadastro das condições, características e necessidades educacionais dos estudantes, com a finalidade de apoiar o acompanhamento, os encaminhamentos e a oferta de atendimentos adequados, como deficiência, TEA, TDAH, altas habilidades/superdotação e dificuldades de aprendizagem."
            actions={can.create ? (
                <TableListCreateButton
                    href={`${deficiencyBase}/create`}
                    ariaLabel="Cadastrar perfil de atendimento"
                />
            ) : undefined}
        >
            <TableListCard
                aria-label="Listagem de perfis de atendimento"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando perfis de atendimento"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="deficiencyIndex__filters">
                        <FormField
                            id="deficiency-filter-name"
                            label="Nome"
                            error={pageErrors.name}
                        >
                            <TextInput
                                id="deficiency-filter-name"
                                name="name"
                                value={values.name}
                                maxLength={255}
                                placeholder="Nome..."
                                invalid={!!pageErrors.name}
                                aria-describedby={pageErrors.name ? "deficiency-filter-name-error" : undefined}
                                onChange={(event) => update("name", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="deficiency-filter-cid"
                            label="Código CID"
                            error={pageErrors.cid_code}
                        >
                            <TextInput
                                id="deficiency-filter-cid"
                                name="cid_code"
                                value={values.cid_code}
                                maxLength={20}
                                placeholder="Código CID..."
                                invalid={!!pageErrors.cid_code}
                                aria-describedby={pageErrors.cid_code ? "deficiency-filter-cid-error" : undefined}
                                onChange={(event) => update("cid_code", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="deficiency-filter-status"
                            label="Status"
                            error={pageErrors.is_active}
                        >
                            <SelectField
                                id="deficiency-filter-status"
                                name="is_active"
                                label="Status"
                                placeholder="Todos"
                                value={values.is_active}
                                options={statusOptions}
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "deficiency-filter-status-error" : undefined}
                                onChange={(event) => update("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de perfis de atendimento">
                    <TableListTable
                        className="deficiencyIndex__table"
                        columns={columns}
                        page={deficiencies}
                    >
                        {deficiencies.data.length ? deficiencies.data.map((deficiency) => (
                            <tr key={deficiency.id}>
                                <td>
                                    <span className="deficiencyIndex__identity">
                                        <strong>{deficiency.name}</strong>
                                        <small>{deficiency.cid_code || "S/ CID"}</small>
                                    </span>
                                </td>
                                <td>
                                    <TableLabel tone={deficiency.is_active ? "success" : "inverse"}>
                                        {deficiency.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
                                <td>{deficiency.cid_code || "Não informado"}</td>
                                <td>{formatTableListText(deficiency.description)}</td>
                                <td>
                                    <TableActions>
                                        {can.view ? (
                                            <Button
                                                href={`${deficiencyBase}/${deficiency.id}/show`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                aria-label={`Visualizar ${deficiency.name}`}
                                            />
                                        ) : null}
                                        {can.update ? (
                                            <Button
                                                size="sm"
                                                tone="inverse"
                                                iconOnly
                                                leftIconClass={deficiency.is_active ? "fa fa-toggle-on" : "fa fa-toggle-off"}
                                                aria-label={`${deficiency.is_active ? "Desativar" : "Ativar"} ${deficiency.name}`}
                                                disabled={statusChange.processing}
                                                onClick={() => toggleActive(deficiency)}
                                            />
                                        ) : null}
                                        {can.delete ? (
                                            <Button
                                                tone="dark-red"
                                                size="sm"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                aria-label={`Excluir ${deficiency.name}`}
                                                disabled={removal.processing}
                                                onClick={() => {
                                                    cancelScheduled();
                                                    removal.open(deficiency);
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
                                    Nenhum perfil encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={deficiencies}
                    disabled={list.loading}
                    paginationLabel="Paginação de perfis de atendimento"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Perfil de Atendimento"
                message={(
                    <>
                        Deseja excluir o perfil <strong>{removal.target?.name}</strong>?
                    </>
                )}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo perfil..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (removal.target) removal.submit("delete", `${deficiencyBase}/${removal.target.id}`);
                }}
            />
        </DeficiencyPage>
    );
}
