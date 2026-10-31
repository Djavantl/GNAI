import { useMemo } from "react";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
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
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import BarrierPage from "./BarrierPage";
import type { BarrierFilters, BarrierPaginator, BarrierRecord } from "./Barrier.types";
import "./Barriers.css";

type Props = {
    barriers: BarrierPaginator;
    filters: Partial<BarrierFilters>;
    can: { create: boolean; show: boolean; destroy: boolean };
};
const base = "/inclusive-radar/barriers";
const emptyFilters: BarrierFilters = { name: "", category: "", priority: "", status: "" };
const priorities = [
    { value: "low", label: "Baixa" },
    { value: "medium", label: "Média" },
    { value: "high", label: "Alta" },
    { value: "urgent", label: "Urgente" },
];
const statuses = [
    { value: "identified", label: "Identificada" },
    { value: "under_analysis", label: "Em Análise" },
    { value: "in_progress", label: "Em Tratamento" },
    { value: "resolved", label: "Resolvida" },
    { value: "not_applicable", label: "Não Aplicável" },
];
const priorityLabels = Object.fromEntries(priorities.map(({ value, label }) => [value, label]));
const statusLabels = Object.fromEntries(statuses.map(({ value, label }) => [value, label]));
const columns: TableListColumn[] = [
    { id: "name", label: "Nome", width: "31%", minWidth: 130, fixed: "start" },
    { id: "category", label: "Categoria", width: "22%", minWidth: 100 },
    { id: "priority", label: "Prioridade", width: "16%", minWidth: 90 },
    { id: "status", label: "Status", width: "16%", minWidth: 100 },
    { id: "location", label: "Ponto de referência", width: "16%", defaultVisible: false },
    { id: "identified", label: "Identificada em", width: "16%", defaultVisible: false },
    { id: "reporter", label: "Relator", width: "16%", defaultVisible: false },
    { id: "description", label: "Descrição", width: "16%", defaultVisible: false },
    { id: "audience", label: "Público impactado", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "15%", minWidth: 86, fixed: "end" },
];

export default function InclusionBarriersIndexPage({ barriers, filters: initialFilters, can }: Props): React.JSX.Element {
    const list = useTableListVisit({
        url: base,
        failureText: "Não foi possível atualizar as barreiras. Tente novamente.",
    });
    const removal = useTableListAction<BarrierRecord>({
        failureText: "Não foi possível excluir a barreira. Tente novamente.",
    });
    const initialValues = useMemo(() => ({ ...emptyFilters, ...initialFilters }), [initialFilters]);
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (next) => list.visit(next),
    });

    return (
        <BarrierPage
            title="Mapa de Barreiras"
            subtitle="Contribuições da comunidade para uma instituição mais acessível."
            actions={can.create ? <TableListCreateButton href={`${base}/create`} ariaLabel="Relatar barreira" /> : undefined}
        >
            <TableListCard aria-label="Listagem de barreiras" aria-busy={list.loading} loading={list.loading} loadingLabel="Atualizando barreiras">
                <FormLayout onSubmit={(event) => { event.preventDefault(); submit(); }}>
                    <div className="barrierIndex__filters">
                        <FormField id="barrier-filter-name" label="Nome da barreira">
                            <TextInput id="barrier-filter-name" name="name" value={values.name} maxLength={255} placeholder="Filtrar por nome da barreira..." onChange={(event) => update("name", event.target.value)} />
                        </FormField>
                        <FormField id="barrier-filter-category" label="Categoria">
                            <TextInput id="barrier-filter-category" name="category" value={values.category} maxLength={255} placeholder="Filtrar por categoria..." onChange={(event) => update("category", event.target.value)} />
                        </FormField>
                        <FormField id="barrier-filter-priority" label="Prioridade">
                            <SelectField id="barrier-filter-priority" name="priority" label="Prioridade" placeholder="Todas" value={values.priority} options={priorities} onChange={(event) => update("priority", event.target.value)} />
                        </FormField>
                        <FormField id="barrier-filter-status" label="Status">
                            <SelectField id="barrier-filter-status" name="status" label="Status" placeholder="Todos" value={values.status} options={statuses} onChange={(event) => update("status", event.target.value)} />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de barreiras">
                    <TableListTable className="barrierIndex__table" columns={columns} page={barriers}>
                        {barriers.data.length ? barriers.data.map((barrier) => {
                            const status = barrier.inspections?.[0]?.status ?? barrier.status ?? "";
                            return (
                                <tr key={barrier.id}>
                                    <td>{barrier.name}</td>
                                    <td>{barrier.category?.name ?? "Não informado"}</td>
                                    <td><TableLabel tone={barrier.priority === "urgent" || barrier.priority === "high" ? "danger" : barrier.priority === "low" ? "info" : "warning"}>{priorityLabels[barrier.priority] ?? barrier.priority}</TableLabel></td>
                                    <td><TableLabel tone={status === "resolved" ? "success" : status === "in_progress" ? "warning" : status === "not_applicable" ? "danger" : "inverse"}>{statusLabels[status] ?? "Pendente"}</TableLabel></td>
                                    <td>{barrier.location?.name || "Não informado"}</td>
                                    <td>{formatTableListDate(barrier.identified_at)}</td>
                                    <td>{barrier.is_anonymous ? "Contribuidor Anônimo" : (barrier.registered_by?.name || "Usuário não identificado")}</td>
                                    <td>{formatTableListText(barrier.description)}</td>
                                    <td>{barrier.deficiencies?.map((entry) => entry.name).join(", ") || "Não informado"}</td>
                                    <td>
                                        <TableActions>
                                            {can.show ? <Button href={`${base}/${barrier.id}`} size="sm" tone="dark-blue" iconOnly leftIconClass="fa fa-eye" aria-label={`Visualizar ${barrier.name}`} /> : null}
                                            {can.destroy ? <Button tone="dark-red" size="sm" iconOnly leftIconClass="fa fa-trash-o" aria-label={`Excluir ${barrier.name}`} onClick={() => { cancelScheduled(); removal.open(barrier); }} /> : null}
                                            {!can.show && !can.destroy ? <span>Sem ações</span> : null}
                                        </TableActions>
                                    </td>
                                </tr>
                            );
                        }) : <tr><TableListEmpty colSpan={columns.length}>Nenhuma barreira identificada até o momento.</TableListEmpty></tr>}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter page={barriers} disabled={list.loading} paginationLabel="Paginação de barreiras" onSelect={(page) => list.visit(values, page)} />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Barreira"
                message={<>O registro <strong>{removal.target?.name}</strong> será excluído permanentemente.</>}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo barreira..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (removal.target) removal.submit("delete", `${base}/${removal.target.id}`);
                }}
            />
        </BarrierPage>
    );
}
