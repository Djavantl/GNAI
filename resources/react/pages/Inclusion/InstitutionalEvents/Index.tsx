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
import InstitutionalEventPage from "./InstitutionalEventPage";
import {
    scheduleStatusLabels,
    type InstitutionalEventPaginator,
    type InstitutionalEventRecord,
    type SelectOption,
} from "./InstitutionalEvent.types";
import "./InstitutionalEvents.css";

type Filters = { title: string; is_active: string; type: string };
type Props = {
    events: InstitutionalEventPaginator;
    filters: Partial<Filters>;
    types: SelectOption[];
    can: { create: boolean; show: boolean; destroy: boolean };
};

const base = "/inclusive-radar/institutional-events";
const emptyFilters: Filters = { title: "", is_active: "", type: "" };
const columns: TableListColumn[] = [
    { id: "title", label: "Título", width: "32%", minWidth: 140, fixed: "start" },
    { id: "type", label: "Tipo", width: "12%", minWidth: 90 },
    { id: "published", label: "Publicação", width: "13%", minWidth: 100 },
    { id: "period", label: "Período", width: "19%", minWidth: 120 },
    { id: "status", label: "Situação", width: "12%", minWidth: 90 },
    { id: "category", label: "Categoria", width: "14%", defaultVisible: false },
    { id: "location", label: "Local", width: "16%", defaultVisible: false },
    { id: "author", label: "Autor", width: "16%", defaultVisible: false },
    { id: "organizer", label: "Organizador", width: "16%", defaultVisible: false },
    { id: "audience", label: "Público", width: "16%", defaultVisible: false },
    { id: "summary", label: "Resumo", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "12%", minWidth: 100, fixed: "end" },
];

function periodLabel(event: InstitutionalEventRecord): string {
    if (!event.has_schedule || !event.start_date) return "Sem datas";

    const dates = event.start_date === event.end_date
        ? event.start_date_label
        : `${event.start_date_label} a ${event.end_date_label}`;

    return event.start_time ? `${dates}, ${event.start_time}–${event.end_time ?? ""}` : dates;
}

export default function InclusionInstitutionalEventsIndexPage({
    events,
    filters: initialFilters,
    types,
    can,
}: Props): React.JSX.Element {
    const { errors: pageErrors } = usePage().props;
    const errors = pageErrors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: base,
        failureText: "Não foi possível atualizar as publicações. Tente novamente.",
    });
    const removal = useTableListAction<InstitutionalEventRecord>({
        failureText: "Não foi possível excluir a publicação. Tente novamente.",
    });
    const initialValues = useMemo(
        () => ({
            ...emptyFilters,
            ...initialFilters,
            is_active: String(initialFilters.is_active ?? ""),
            type: String(initialFilters.type ?? ""),
        }),
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
        <InstitutionalEventPage
            title="Publicações"
            subtitle="Notícias, eventos, comunicados e registros exibidos no site."
            actions={can.create ? (
                <TableListCreateButton href={`${base}/create`} ariaLabel="Cadastrar publicação" />
            ) : undefined}
        >
            <TableListCard
                aria-label="Listagem de publicações"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando as publicações"
            >
                <FormLayout onSubmit={(event) => { event.preventDefault(); submitFilters(); }}>
                    <div className="institutionalEventIndex__filters">
                        <FormField id="institutional-event-title-filter" label="Título" error={errors.title}>
                            <TextInput
                                id="institutional-event-title-filter"
                                name="title"
                                value={filters.title}
                                maxLength={255}
                                placeholder="Filtrar por título..."
                                invalid={!!errors.title}
                                aria-describedby={errors.title ? "institutional-event-title-filter-error" : undefined}
                                onChange={(event) => updateFilter("title", event.target.value)}
                            />
                        </FormField>
                        <FormField id="institutional-event-type-filter" label="Tipo" error={errors.type}>
                            <SelectField
                                id="institutional-event-type-filter"
                                name="type"
                                label="Tipo"
                                placeholder="Todos"
                                value={filters.type}
                                options={types}
                                aria-invalid={!!errors.type}
                                aria-describedby={errors.type ? "institutional-event-type-filter-error" : undefined}
                                onChange={(event) => updateFilter("type", event.target.value)}
                            />
                        </FormField>
                        <FormField id="institutional-event-status-filter" label="Situação" error={errors.is_active}>
                            <SelectField
                                id="institutional-event-status-filter"
                                name="is_active"
                                label="Situação"
                                placeholder="Todas"
                                value={filters.is_active}
                                options={[
                                    { value: "1", label: "Publicado" },
                                    { value: "0", label: "Rascunho" },
                                ]}
                                aria-invalid={!!errors.is_active}
                                aria-describedby={errors.is_active ? "institutional-event-status-filter-error" : undefined}
                                onChange={(event) => updateFilter("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clearFilters} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de publicações">
                    <TableListTable
                        className="institutionalEventIndex__table"
                        columns={columns}
                        page={events}
                    >
                        {events.data.map((event) => (
                            <tr key={event.id}>
                                <td>
                                    {event.title}
                                    {event.is_featured ? (
                                        <span className="institutionalEventIndex__featured">
                                            <i className="fa fa-star" aria-hidden="true" /> Destaque
                                        </span>
                                    ) : null}
                                </td>
                                <td>{event.type_label}</td>
                                <td>{event.published_at_label}</td>
                                <td>
                                    {periodLabel(event)}
                                    {event.schedule_status ? (
                                        <span className="institutionalEventIndex__schedule">
                                            {scheduleStatusLabels[event.schedule_status]}
                                        </span>
                                    ) : null}
                                </td>
                                <td>
                                    <TableLabel tone={event.is_active ? "success" : "inverse"}>
                                        {event.is_active ? "Publicado" : "Rascunho"}
                                    </TableLabel>
                                </td>
                                <td>{event.category || "Não informada"}</td>
                                <td>{event.location || "Não informado"}</td>
                                <td>{event.author_name || "Não informado"}</td>
                                <td>{event.organizer || "Não informado"}</td>
                                <td>{event.audience || "Não informado"}</td>
                                <td>{formatTableListText(event.summary)}</td>
                                <td>
                                    <TableActions>
                                        {can.show ? (
                                            <Button
                                                href={`${base}/${event.id}`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                title={`Visualizar publicação ${event.title}`}
                                                aria-label={`Visualizar publicação ${event.title}`}
                                            />
                                        ) : null}
                                        {event.public_url ? (
                                            <Button
                                                href={event.public_url}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                size="sm"
                                                tone="list-action"
                                                iconOnly
                                                leftIconClass="fa fa-external-link"
                                                title={`Abrir ${event.title} no site`}
                                                aria-label={`Abrir ${event.title} no site (nova aba)`}
                                            />
                                        ) : null}
                                        {can.destroy ? (
                                            <Button
                                                size="sm"
                                                tone="dark-red"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                disabled={removal.processing}
                                                title={`Excluir publicação ${event.title}`}
                                                aria-label={`Excluir publicação ${event.title}`}
                                                onClick={() => removal.open(event)}
                                            />
                                        ) : null}
                                        {!can.show && !can.destroy ? <span>Nenhuma ação</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        ))}
                        {!events.data.length ? (
                            <tr><TableListEmpty colSpan={columns.length}>Nenhuma publicação cadastrada.</TableListEmpty></tr>
                        ) : null}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={events}
                    disabled={list.loading}
                    paginationLabel="Páginas de publicações"
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
                title="Excluir Publicação"
                message={<>A publicação <strong>{removal.target?.title}</strong> será excluída permanentemente, com sua capa e galeria.</>}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo publicação..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (!removal.target) return;
                    cancelScheduled();
                    removal.submit("delete", `${base}/${removal.target.id}`);
                }}
            />
        </InstitutionalEventPage>
    );
}
