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
    TableListMoreFilters,
    TableListScroll,
    TableListTable,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters, { compactListFilters } from "@/hooks/useAutoListFilters";
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
    { id: "actions", label: "Ações", width: "8%", minWidth: 76, fixed: "end" },
];

function itemHref(waitlist: WaitlistRecord): string {
    const basePath = waitlist.waitlistable_type === "assistive_technology"
        ? "/inclusive-radar/assistive-technologies"
        : "/inclusive-radar/accessible-educational-materials";
    return `${basePath}/${waitlist.waitlistable_id}`;
}

export default function InclusiveRadarWaitlistsIndexPage({
    waitlists,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const { errors: pageErrors } = usePage().props;
    const errors = pageErrors as Record<string, string | undefined>;
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [failure, setFailure] = useState("");
    const [waitlistToDelete, setWaitlistToDelete] = useState<WaitlistRecord | null>(null);
    const pending = useRef(false);
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
        onSearch: visit,
    });

    useEffect(() => {
        if (!failure) return;
        notify({ tone: "error", title: "Não foi possível concluir", text: failure });
    }, [failure]);

    function visit(values: Filters, url = base): void {
        if (pending.current) return;
        pending.current = true;
        setBusy(true);
        setListLoading(true);
        setFailure("");
        router.get(url, url === base ? compactListFilters(values) : {}, {
            preserveState: true,
            preserveScroll: true,
            onError: () => setFailure("Não foi possível atualizar a fila de espera. Tente novamente."),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
                setListLoading(false);
            },
        });
    }

    function remove(): void {
        if (pending.current || !waitlistToDelete) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.delete(`${base}/${waitlistToDelete.id}`, {
            preserveScroll: true,
            onError: () => setFailure("Não foi possível excluir a solicitação. Tente novamente."),
            onSuccess: () => setWaitlistToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

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
                aria-busy={busy}
                loading={listLoading}
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
                                disabled={busy}
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
                                disabled={busy}
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
                                disabled={busy}
                                invalid={!!errors.professional}
                                aria-describedby={errors.professional ? "waitlist-professional-filter-error" : undefined}
                                onChange={(event) => updateFilter("professional", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton disabled={busy} onClick={clearFilters} />
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
                                    disabled={busy}
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
                        summary={`${waitlists.from ?? 0}–${waitlists.to ?? 0} de ${waitlists.total} registros`}
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
                                                    disabled={busy}
                                                    title={`Excluir solicitação de ${waitlist.item_name}`}
                                                    aria-label={`Excluir solicitação de ${waitlist.item_name}`}
                                                    onClick={() => setWaitlistToDelete(waitlist)}
                                                />
                                            ) : null}
                                            {!can.show && !can.destroy ? <span>Nenhuma ação</span> : null}
                                        </TableActions>
                                    </td>
                                </tr>
                            );
                        })}
                        {!waitlists.data.length ? (
                            <tr><TableListEmpty colSpan={6}>Nenhuma solicitação registrada.</TableListEmpty></tr>
                        ) : null}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    summary={`${waitlists.from ?? 0}–${waitlists.to ?? 0} de ${waitlists.total}`}
                    currentPage={waitlists.current_page}
                    pageCount={waitlists.last_page}
                    disabled={busy}
                    paginationLabel="Páginas da fila de espera"
                    onSelect={(page) => {
                        cancelScheduled();
                        const params = new URLSearchParams(window.location.search);
                        params.set("page", String(page));
                        visit(filters, `${base}?${params.toString()}`);
                    }}
                />
            </TableListCard>
            <ConfirmActionModal
                open={waitlistToDelete !== null}
                title="Excluir Solicitação"
                message={<>Excluir permanentemente a solicitação <strong>#{waitlistToDelete?.id}</strong>?</>}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo solicitação..."
                confirmTone="dark-red"
                onClose={() => { setFailure(""); setWaitlistToDelete(null); }}
                onConfirm={remove}
            />
        </WaitlistPage>
    );
}
