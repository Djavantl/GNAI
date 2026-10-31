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
import PublicationPage from "./PublicationPage";
import {
    scheduleStatusLabels,
    type PublicationPaginator,
    type PublicationRecord,
    type SelectOption,
} from "./Publication.types";
import "./Publications.css";

type Filters = { title: string; is_active: string; type: string };
type Props = {
    publications: PublicationPaginator;
    filters: Partial<Filters>;
    types: SelectOption[];
    can: { create: boolean; show: boolean; destroy: boolean };
};

const base = "/inclusive-radar/publications";
const emptyFilters: Filters = { title: "", is_active: "", type: "" };
const columns: TableListColumn[] = [
    { id: "title", label: "Título", width: "32%", minWidth: 140, fixed: "start" },
    { id: "type", label: "Tipo", width: "12%", minWidth: 90 },
    { id: "published", label: "Publicação", width: "13%", minWidth: 100 },
    { id: "period", label: "Período", width: "19%", minWidth: 120 },
    { id: "status", label: "Situação", width: "12%", minWidth: 90 },
    { id: "tags", label: "Etiquetas", width: "14%", defaultVisible: false },
    { id: "location", label: "Local", width: "16%", defaultVisible: false },
    { id: "author", label: "Autor", width: "16%", defaultVisible: false },
    { id: "organizer", label: "Organizador", width: "16%", defaultVisible: false },
    { id: "audience", label: "Público", width: "16%", defaultVisible: false },
    { id: "summary", label: "Resumo", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "12%", minWidth: 100, fixed: "end" },
];

function periodLabel(publication: PublicationRecord): string {
    if (!publication.has_schedule || !publication.start_date) return "Sem datas";

    const dates = publication.start_date === publication.end_date
        ? publication.start_date_label
        : `${publication.start_date_label} a ${publication.end_date_label}`;

    return publication.start_time ? `${dates}, ${publication.start_time} a ${publication.end_time ?? ""}` : dates;
}

export default function InclusionPublicationsIndexPage({
    publications,
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
    const removal = useTableListAction<PublicationRecord>({
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
        <PublicationPage
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
                    <div className="publicationIndex__filters">
                        <FormField id="publication-title-filter" label="Título" error={errors.title}>
                            <TextInput
                                id="publication-title-filter"
                                name="title"
                                value={filters.title}
                                maxLength={255}
                                placeholder="Filtrar por título..."
                                invalid={!!errors.title}
                                aria-describedby={errors.title ? "publication-title-filter-error" : undefined}
                                onChange={(event) => updateFilter("title", event.target.value)}
                            />
                        </FormField>
                        <FormField id="publication-type-filter" label="Tipo" error={errors.type}>
                            <SelectField
                                id="publication-type-filter"
                                name="type"
                                label="Tipo"
                                placeholder="Todos"
                                value={filters.type}
                                options={types}
                                aria-invalid={!!errors.type}
                                aria-describedby={errors.type ? "publication-type-filter-error" : undefined}
                                onChange={(event) => updateFilter("type", event.target.value)}
                            />
                        </FormField>
                        <FormField id="publication-status-filter" label="Situação" error={errors.is_active}>
                            <SelectField
                                id="publication-status-filter"
                                name="is_active"
                                label="Situação"
                                placeholder="Todas"
                                value={filters.is_active}
                                options={[
                                    { value: "1", label: "Publicado" },
                                    { value: "0", label: "Rascunho" },
                                ]}
                                aria-invalid={!!errors.is_active}
                                aria-describedby={errors.is_active ? "publication-status-filter-error" : undefined}
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
                        className="publicationIndex__table"
                        columns={columns}
                        page={publications}
                    >
                        {publications.data.map((publication) => (
                            <tr key={publication.id}>
                                <td>
                                    {publication.title}
                                    {publication.is_featured ? (
                                        <span className="publicationIndex__featured">
                                            <i className="fa fa-star" aria-hidden="true" /> Destaque
                                        </span>
                                    ) : null}
                                </td>
                                <td>{publication.type_label}</td>
                                <td>{publication.published_at_label}</td>
                                <td>
                                    {periodLabel(publication)}
                                    {publication.schedule_status ? (
                                        <span className="publicationIndex__schedule">
                                            {scheduleStatusLabels[publication.schedule_status]}
                                        </span>
                                    ) : null}
                                </td>
                                <td>
                                    <TableLabel tone={publication.is_active ? "success" : "inverse"}>
                                        {publication.is_active ? "Publicado" : "Rascunho"}
                                    </TableLabel>
                                </td>
                                <td>{publication.tags.map((tag) => tag.name).join(", ") || "Nenhuma"}</td>
                                <td>{publication.location || "Não informado"}</td>
                                <td>{publication.author_name || "Não informado"}</td>
                                <td>{publication.organizer || "Não informado"}</td>
                                <td>{publication.audience || "Não informado"}</td>
                                <td>{formatTableListText(publication.summary)}</td>
                                <td>
                                    <TableActions>
                                        {can.show ? (
                                            <Button
                                                href={`${base}/${publication.id}`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                title={`Visualizar publicação ${publication.title}`}
                                                aria-label={`Visualizar publicação ${publication.title}`}
                                            />
                                        ) : null}
                                        {publication.public_url ? (
                                            <Button
                                                href={publication.public_url}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                size="sm"
                                                tone="list-action"
                                                iconOnly
                                                leftIconClass="fa fa-external-link"
                                                title={`Abrir ${publication.title} no site`}
                                                aria-label={`Abrir ${publication.title} no site (nova aba)`}
                                            />
                                        ) : null}
                                        {can.destroy ? (
                                            <Button
                                                size="sm"
                                                tone="dark-red"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                disabled={removal.processing}
                                                title={`Excluir publicação ${publication.title}`}
                                                aria-label={`Excluir publicação ${publication.title}`}
                                                onClick={() => removal.open(publication)}
                                            />
                                        ) : null}
                                        {!can.show && !can.destroy ? <span>Nenhuma ação</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        ))}
                        {!publications.data.length ? (
                            <tr><TableListEmpty colSpan={columns.length}>Nenhuma publicação cadastrada.</TableListEmpty></tr>
                        ) : null}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={publications}
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
        </PublicationPage>
    );
}
