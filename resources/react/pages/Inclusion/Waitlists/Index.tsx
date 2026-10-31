import { usePage } from "@inertiajs/react";
import { useMemo } from "react";
import { itemTypeLabels } from "@/pages/Inclusion/Items/Items.types";
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
    TableListMoreFilters,
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
import WaitlistPage from "./WaitlistPage";
import type { WaitlistPaginator, WaitlistRecord } from "./Waitlist.types";
import "./Waitlists.css";

type Filters = {
    item: string;
    student: string;
    professional: string;
    status: string;
};

type Props = {
    waitlists: WaitlistPaginator;
    filters: Partial<Filters>;
    can: { create: boolean; show: boolean; destroy: boolean };
};

const base = "/inclusive-radar/waitlists";
const emptyFilters: Filters = { item: "", student: "", professional: "", status: "" };
const statusOptions = [
    { value: "waiting", label: "Em espera" },
    { value: "notified", label: "Notificado" },
    { value: "fulfilled", label: "Atendido" },
    { value: "cancelled", label: "Cancelado" },
];
const columns: TableListColumn[] = [
    { id: "item", label: "Item", width: "26%", minWidth: 130, fixed: "start" },
    { id: "beneficiary", label: "Beneficiário", width: "23%", minWidth: 120 },
    { id: "requested", label: "Data da Solicitação", width: "18%", minWidth: 110 },
    { id: "status", label: "Status", width: "13%", minWidth: 90 },
    { id: "user", label: "Usuário", width: "12%", minWidth: 80 },
    { id: "type", label: "Tipo do item", width: "16%", defaultVisible: false },
    { id: "asset", label: "Patrimônio / Tombamento", width: "16%", defaultVisible: false },
    { id: "registration", label: "Matrícula do aluno", width: "16%", defaultVisible: false },
    { id: "observation", label: "Observações", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "8%", minWidth: 76, fixed: "end" },
];

function itemHref(waitlist: WaitlistRecord): string {
    return `/inclusive-radar/items/${waitlist.waitlistable_id}`;
}

export default function InclusionWaitlistsIndexPage({
    waitlists,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const { errors: pageErrors } = usePage().props;
    const errors = pageErrors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: base,
        failureText: "Não foi possível atualizar a fila de espera. Tente novamente.",
    });
    const removal = useTableListAction<WaitlistRecord>({
        failureText: "Não foi possível excluir a solicitação. Tente novamente.",
    });
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters }),
        [initialFilters],
    );
    const {
        values: filters,
        update: updateFilter,
        clear: clearFilters,
        submit: submitFilters,
        cancelScheduled,
    } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (values: Filters) => list.visit(values),
    });

    return (
        <WaitlistPage
            title="Fila de Espera"
            subtitle="Gerencie solicitações de recursos que estão indisponíveis para empréstimo."
            actions={can.create ? (
                <TableListCreateButton href={`${base}/create`} ariaLabel="Cadastrar solicitação na fila de espera" />
            ) : undefined}
        >
            <TableListCard
                aria-label="Listagem da fila de espera"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando a fila de espera"
            >
                <FormLayout onSubmit={(event) => { event.preventDefault(); submitFilters(); }}>
                    <div className="waitlistIndex__filters">
                        <FormField id="waitlist-item-filter" label="Item" error={errors.item}>
                            <TextInput
                                id="waitlist-item-filter"
                                name="item"
                                value={filters.item}
                                maxLength={255}
                                placeholder="Filtrar por item..."
                                invalid={!!errors.item}
                                aria-describedby={errors.item ? "waitlist-item-filter-error" : undefined}
                                onChange={(event) => updateFilter("item", event.target.value)}
                            />
                        </FormField>
                        <FormField id="waitlist-student-filter" label="Aluno" error={errors.student}>
                            <TextInput
                                id="waitlist-student-filter"
                                name="student"
                                value={filters.student}
                                maxLength={255}
                                placeholder="Filtrar por aluno..."
                                invalid={!!errors.student}
                                aria-describedby={errors.student ? "waitlist-student-filter-error" : undefined}
                                onChange={(event) => updateFilter("student", event.target.value)}
                            />
                        </FormField>
                        <FormField id="waitlist-professional-filter" label="Profissional" error={errors.professional}>
                            <TextInput
                                id="waitlist-professional-filter"
                                name="professional"
                                value={filters.professional}
                                maxLength={255}
                                placeholder="Filtrar por profissional..."
                                invalid={!!errors.professional}
                                aria-describedby={errors.professional ? "waitlist-professional-filter-error" : undefined}
                                onChange={(event) => updateFilter("professional", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clearFilters} />
                        </TableListFilterActions>
                    </div>
                    <TableListMoreFilters active={Boolean(filters.status)}>
                        <div className="waitlistIndex__statusFilter">
                            <FormField id="waitlist-status-filter" label="Status" error={errors.status}>
                                <SelectField
                                    id="waitlist-status-filter"
                                    name="status"
                                    label="Status"
                                    placeholder="Todos"
                                    value={filters.status}
                                    options={statusOptions}
                                    aria-invalid={!!errors.status}
                                    aria-describedby={errors.status ? "waitlist-status-filter-error" : undefined}
                                    onChange={(event) => updateFilter("status", event.target.value)}
                                />
                            </FormField>
                        </div>
                    </TableListMoreFilters>
                </FormLayout>
                <TableListScroll aria-label="Tabela de solicitações da fila de espera">
                    <TableListTable
                        className="waitlistIndex__table"
                        columns={columns}
                        page={waitlists}
                    >
                        {waitlists.data.map((waitlist) => {
                            const beneficiary = waitlist.student
                                ? `${waitlist.student.name}${waitlist.student.registration ? ` (Matrícula: ${waitlist.student.registration})` : ""}`
                                : waitlist.professional?.name ?? "Não informado";

                            return (
                                <tr key={waitlist.id}>
                                    <td>
                                        <a href={itemHref(waitlist)} target="_blank" rel="noreferrer">
                                            {waitlist.item_name}
                                        </a>
                                    </td>
                                    <td>{beneficiary}</td>
                                    <td>{waitlist.requested_at_label}</td>
                                    <td><TableLabel tone={waitlist.status_color}>{waitlist.status_label}</TableLabel></td>
                                    <td>{waitlist.user}</td>
                                    <td>{itemTypeLabels[waitlist.item_type] ?? "Não informado"}</td>
                                    <td>{waitlist.asset_code || "Não informado"}</td>
                                    <td>{waitlist.student?.registration || "Não informado"}</td>
                                    <td>{formatTableListText(waitlist.observation)}</td>
                                    <td>
                                        <TableActions>
                                            {can.show ? (
                                                <Button
                                                    href={`${base}/${waitlist.id}`}
                                                    size="sm"
                                                    tone="dark-blue"
                                                    iconOnly
                                                    leftIconClass="fa fa-eye"
                                                    title={`Visualizar solicitação de ${waitlist.item_name}`}
                                                    aria-label={`Visualizar solicitação de ${waitlist.item_name}`}
                                                />
                                            ) : null}
                                            {can.destroy ? (
                                                <Button
                                                    size="sm"
                                                    tone="dark-red"
                                                    iconOnly
                                                    leftIconClass="fa fa-trash-o"
                                                    disabled={removal.processing}
                                                    title={`Excluir solicitação de ${waitlist.item_name}`}
                                                    aria-label={`Excluir solicitação de ${waitlist.item_name}`}
                                                    onClick={() => removal.open(waitlist)}
                                                />
                                            ) : null}
                                            {!can.show && !can.destroy ? <span>Nenhuma ação</span> : null}
                                        </TableActions>
                                    </td>
                                </tr>
                            );
                        })}
                        {!waitlists.data.length ? (
                            <tr><TableListEmpty colSpan={columns.length}>Nenhuma solicitação registrada.</TableListEmpty></tr>
                        ) : null}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={waitlists}
                    disabled={list.loading}
                    paginationLabel="Páginas da fila de espera"
                    onSelect={(page) => {
                        cancelScheduled();
                        const params = new URLSearchParams(window.location.search);
                        params.set("page", String(page));
                        list.visitUrl(`${base}?${params.toString()}`);
                    }}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Solicitação"
                message={<>Excluir permanentemente a solicitação <strong>#{removal.target?.id}</strong>?</>}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo solicitação..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (!removal.target) return;
                    cancelScheduled();
                    removal.submit("delete", `${base}/${removal.target.id}`);
                }}
            />
        </WaitlistPage>
    );
}
