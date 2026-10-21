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
import InstitutionalEventPage from "./InstitutionalEventPage";
import type {
    InstitutionalEventPaginator,
    InstitutionalEventRecord,
} from "./InstitutionalEvent.types";
import "./InstitutionalEvents.css";

type Filters = { title: string; is_active: string };
type Props = {
    events: InstitutionalEventPaginator;
    filters: Partial<Filters>;
    can: { create: boolean; show: boolean; destroy: boolean };
};

const base = "/inclusive-radar/institutional-events";
const emptyFilters: Filters = { title: "", is_active: "" };
const columns: TableListColumn[] = [
    { id: "title", label: "Nome", width: "31%", minWidth: 140, fixed: "start" },
    { id: "start", label: "Data Inicial", width: "15%", minWidth: 100 },
    { id: "end", label: "Data Final", width: "15%", minWidth: 100 },
    { id: "time", label: "Horário", width: "17%", minWidth: 100 },
    { id: "status", label: "Status", width: "12%", minWidth: 80 },
    { id: "actions", label: "Ações", width: "10%", minWidth: 76, fixed: "end" },
];

export default function InclusiveRadarInstitutionalEventsIndexPage({
    events,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const { errors: pageErrors } = usePage().props;
    const errors = pageErrors as Record<string, string | undefined>;
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [failure, setFailure] = useState("");
    const [eventToDelete, setEventToDelete] = useState<InstitutionalEventRecord | null>(null);
    const pending = useRef(false);
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters, is_active: String(initialFilters.is_active ?? "") }),
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
            onError: () => setFailure("Não foi possível atualizar a agenda institucional. Tente novamente."),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
                setListLoading(false);
            },
        });
    }

    function remove(): void {
        if (pending.current || !eventToDelete) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.delete(`${base}/${eventToDelete.id}`, {
            preserveScroll: true,
            onError: () => setFailure("Não foi possível excluir o evento. Tente novamente."),
            onSuccess: () => setEventToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    return (
        <InstitutionalEventPage
            title="Agenda Institucional"
            subtitle="Gerenciamento de eventos institucionais."
            actions={can.create ? (
                <TableListCreateButton href={`${base}/create`} ariaLabel="Cadastrar evento na agenda institucional" />
            ) : undefined}
        >
            <TableListCard
                aria-label="Listagem da agenda institucional"
                aria-busy={busy}
                loading={listLoading}
                loadingLabel="Atualizando a agenda institucional"
            >
                <FormLayout onSubmit={(event) => { event.preventDefault(); submitFilters(); }}>
                    <div className="institutionalEventIndex__filters">
                        <FormField id="institutional-event-title-filter" label="Evento" error={errors.title}>
                            <TextInput
                                id="institutional-event-title-filter"
                                name="title"
                                value={filters.title}
                                maxLength={255}
                                placeholder="Filtrar por nome do evento..."
                                disabled={busy}
                                invalid={!!errors.title}
                                aria-describedby={errors.title ? "institutional-event-title-filter-error" : undefined}
                                onChange={(event) => updateFilter("title", event.target.value)}
                            />
                        </FormField>
                        <FormField id="institutional-event-status-filter" label="Status" error={errors.is_active}>
                            <SelectField
                                id="institutional-event-status-filter"
                                name="is_active"
                                label="Status"
                                placeholder="Todos"
                                value={filters.is_active}
                                options={[
                                    { value: "1", label: "Ativo" },
                                    { value: "0", label: "Inativo" },
                                ]}
                                disabled={busy}
                                aria-invalid={!!errors.is_active}
                                aria-describedby={errors.is_active ? "institutional-event-status-filter-error" : undefined}
                                onChange={(event) => updateFilter("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton disabled={busy} onClick={clearFilters} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de eventos institucionais">
                    <TableListTable
                        className="institutionalEventIndex__table"
                        columns={columns}
                        summary={`${events.from ?? 0}–${events.to ?? 0} de ${events.total} registros`}
                    >
                        {events.data.map((event) => (
                            <tr key={event.id}>
                                <td>{event.title}</td>
                                <td>{event.start_date_label}</td>
                                <td>{event.end_date_label}</td>
                                <td>{event.start_time} – {event.end_time}</td>
                                <td>
                                    <TableLabel tone={event.is_active ? "success" : "inverse"}>
                                        {event.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
                                <td>
                                    <TableActions>
                                        {can.show ? (
                                            <Button
                                                href={`${base}/${event.id}`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                title={`Visualizar evento ${event.title}`}
                                                aria-label={`Visualizar evento ${event.title}`}
                                            />
                                        ) : null}
                                        {can.destroy ? (
                                            <Button
                                                size="sm"
                                                tone="dark-red"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                disabled={busy}
                                                title={`Excluir evento ${event.title}`}
                                                aria-label={`Excluir evento ${event.title}`}
                                                onClick={() => setEventToDelete(event)}
                                            />
                                        ) : null}
                                        {!can.show && !can.destroy ? <span>Nenhuma ação</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        ))}
                        {!events.data.length ? (
                            <tr><TableListEmpty colSpan={6}>Nenhum evento cadastrado.</TableListEmpty></tr>
                        ) : null}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    summary={`${events.from ?? 0}–${events.to ?? 0} de ${events.total}`}
                    currentPage={events.current_page}
                    pageCount={events.last_page}
                    disabled={busy}
                    paginationLabel="Páginas da agenda institucional"
                    onSelect={(page) => {
                        cancelScheduled();
                        const params = new URLSearchParams(window.location.search);
                        params.set("page", String(page));
                        visit(filters, `${base}?${params.toString()}`);
                    }}
                />
            </TableListCard>
            <ConfirmActionModal
                open={eventToDelete !== null}
                title="Excluir Evento Institucional"
                message={<>O evento <strong>{eventToDelete?.title}</strong> será excluído permanentemente.</>}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo evento..."
                confirmTone="dark-red"
                onClose={() => { setFailure(""); setEventToDelete(null); }}
                onConfirm={remove}
            />
        </InstitutionalEventPage>
    );
}
