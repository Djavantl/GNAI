import { usePage } from "@inertiajs/react";
import { useMemo } from "react";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
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
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import LocationPage from "./LocationPage";
import type { LocationFilters, LocationPaginator, LocationRecord } from "./Location.types";
import "./Locations.css";

type Props = {
    locations: LocationPaginator;
    filters: Partial<LocationFilters>;
    can: { create: boolean; show: boolean; destroy: boolean };
};

const base = "/inclusive-radar/locations";
const emptyFilters: LocationFilters = { name: "", institution_name: "", is_active: "" };
const columns: TableListColumn[] = [
    { id: "name", label: "Nome", width: "29%", minWidth: 120, fixed: "start" },
    { id: "institution", label: "Instituição", width: "25%", minWidth: 110 },
    { id: "type", label: "Tipo", width: "18%", minWidth: 90 },
    { id: "status", label: "Status", width: "13%", minWidth: 80 },
    { id: "description", label: "Descrição", width: "16%", defaultVisible: false },
    { id: "latitude", label: "Latitude", width: "16%", defaultVisible: false },
    { id: "longitude", label: "Longitude", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "15%", minWidth: 86, fixed: "end" },
];

export default function InclusionLocationsIndexPage({ locations, filters: initialFilters, can }: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: base,
        failureText: "Não foi possível atualizar os pontos de referência. Tente novamente.",
    });
    const removal = useTableListAction<LocationRecord>({
        failureText: "Não foi possível excluir o ponto de referência. Tente novamente.",
    });
    const initialValues = useMemo(() => ({ ...emptyFilters, ...initialFilters }), [initialFilters]);
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (filters) => list.visit(filters),
    });

    return (
        <LocationPage
            title="Pontos de Referência"
            subtitle="Gerencie os prédios, salas e locais específicos dentro de cada instituição."
            actions={can.create ? <TableListCreateButton href={`${base}/create`} ariaLabel="Cadastrar ponto de referência" /> : undefined}
        >
            <TableListCard aria-label="Listagem de pontos de referência" aria-busy={list.loading} loading={list.loading} loadingLabel="Atualizando pontos de referência">
                <FormLayout onSubmit={(event) => { event.preventDefault(); submit(); }}>
                    <div className="locationIndex__filters">
                        <FormField id="location-filter-name" label="Nome do Local" error={pageErrors.name}>
                            <TextInput
                                id="location-filter-name"
                                name="name"
                                value={values.name}
                                maxLength={255}
                                placeholder="Filtrar por nome do local..."
                                invalid={!!pageErrors.name}
                                aria-describedby={pageErrors.name ? "location-filter-name-error" : undefined}
                                onChange={(event) => update("name", event.target.value)}
                            />
                        </FormField>
                        <FormField id="location-filter-institution" label="Instituição" error={pageErrors.institution_name}>
                            <TextInput
                                id="location-filter-institution"
                                name="institution_name"
                                value={values.institution_name}
                                maxLength={255}
                                placeholder="Filtrar por instituição..."
                                invalid={!!pageErrors.institution_name}
                                aria-describedby={pageErrors.institution_name ? "location-filter-institution-error" : undefined}
                                onChange={(event) => update("institution_name", event.target.value)}
                            />
                        </FormField>
                        <FormField id="location-filter-status" label="Status" error={pageErrors.is_active}>
                            <SelectField
                                id="location-filter-status"
                                name="is_active"
                                label="Status"
                                placeholder="Todos"
                                value={values.is_active}
                                options={[{ value: "1", label: "Ativo" }, { value: "0", label: "Inativo" }]}
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "location-filter-status-error" : undefined}
                                onChange={(event) => update("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de pontos de referência">
                    <TableListTable
                        className="locationIndex__table"
                        columns={columns}
                        page={locations}
                    >
                        {locations.data.length ? locations.data.map((location) => (
                            <tr key={location.id}>
                                <td>{location.name || "N/A"}</td>
                                <td>{location.institution?.name || "N/A"}</td>
                                <td>{location.type || "Não informado"}</td>
                                <td>
                                    <TableLabel tone={location.is_active ? "success" : "inverse"}>
                                        {location.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
                                <td>{formatTableListText(location.description)}</td>
                                <td>{location.latitude ?? "Não informado"}</td>
                                <td>{location.longitude ?? "Não informado"}</td>
                                <td>
                                    <TableActions>
                                        {can.show ? (
                                            <Button
                                                href={`${base}/${location.id}`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                aria-label={`Visualizar ${location.name}`}
                                            />
                                        ) : null}
                                        {can.destroy ? (
                                            <Button
                                                tone="dark-red"
                                                size="sm"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                aria-label={`Excluir ${location.name}`}
                                                onClick={() => {
                                                    cancelScheduled();
                                                    removal.open(location);
                                                }}
                                            />
                                        ) : null}
                                        {!can.show && !can.destroy ? <span>Sem ações</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr><TableListEmpty colSpan={columns.length}>Nenhum ponto de referência encontrado.</TableListEmpty></tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={locations}
                    disabled={list.loading}
                    paginationLabel="Paginação de pontos de referência"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Ponto de Referência"
                message={<>O ponto <strong>{removal.target?.name}</strong> será excluído permanentemente.</>}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo ponto..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (removal.target) removal.submit("delete", `${base}/${removal.target.id}`);
                }}
            />
        </LocationPage>
    );
}
