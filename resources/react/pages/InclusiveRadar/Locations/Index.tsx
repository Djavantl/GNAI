import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useRef, useState } from "react";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
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
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import { notify } from "@/components/UI/NotificationToast/notify";
import useAutoListFilters, { compactListFilters } from "@/hooks/useAutoListFilters";
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
    { id: "actions", label: "Ações", width: "15%", minWidth: 86, fixed: "end" },
];

export default function InclusiveRadarLocationsIndexPage({ locations, filters: initialFilters, can }: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const pending = useRef(false);
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [failure, setFailure] = useState("");
    const [locationToDelete, setLocationToDelete] = useState<LocationRecord | null>(null);
    const initialValues = useMemo(() => ({ ...emptyFilters, ...initialFilters }), [initialFilters]);
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (filters) => visit(filters),
    });

    useEffect(() => {
        if (failure) notify({ tone: "error", title: "Não foi possível concluir", text: failure });
    }, [failure]);

    function visit(filters: LocationFilters, page = 1): void {
        if (pending.current) return;
        pending.current = true;
        setBusy(true);
        setListLoading(true);
        setFailure("");
        router.get(base, { ...compactListFilters(filters), page }, {
            preserveState: true,
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível atualizar os pontos de referência. Tente novamente.");
                return false;
            },
            onFinish: () => {
                pending.current = false;
                setBusy(false);
                setListLoading(false);
            },
        });
    }

    function remove(): void {
        if (pending.current || !locationToDelete) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.delete(`${base}/${locationToDelete.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir o ponto de referência. Tente novamente.");
                return false;
            },
            onSuccess: () => setLocationToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    return (
        <LocationPage
            title="Pontos de Referência"
            subtitle="Gerencie os prédios, salas e locais específicos dentro de cada instituição."
            actions={can.create ? <TableListCreateButton href={`${base}/create`} ariaLabel="Cadastrar ponto de referência" /> : undefined}
        >
            <TableListCard aria-label="Listagem de pontos de referência" aria-busy={busy} loading={listLoading} loadingLabel="Atualizando pontos de referência">
                <FormLayout onSubmit={(event) => { event.preventDefault(); submit(); }}>
                    <div className="locationIndex__filters">
                        <FormField id="location-filter-name" label="Nome do Local" error={pageErrors.name}>
                            <TextInput
                                id="location-filter-name"
                                name="name"
                                value={values.name}
                                maxLength={255}
                                placeholder="Filtrar por nome do local..."
                                disabled={busy}
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
                                disabled={busy}
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
                                disabled={busy}
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "location-filter-status-error" : undefined}
                                onChange={(event) => update("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton disabled={busy} onClick={clear} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de pontos de referência">
                    <TableListTable
                        className="locationIndex__table"
                        columns={columns}
                        summary={`${locations.from ?? 0}–${locations.to ?? 0} de ${locations.total} registros`}
                    >
                        {locations.data.length ? locations.data.map((location) => (
                            <tr key={location.id}>
                                <td>{location.name || "N/A"}</td>
                                <td>{location.institution?.name || "N/A"}</td>
                                <td>{location.type || "—"}</td>
                                <td>
                                    <TableLabel tone={location.is_active ? "success" : "inverse"}>
                                        {location.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
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
                                                    setFailure("");
                                                    setLocationToDelete(location);
                                                }}
                                            />
                                        ) : null}
                                        {!can.show && !can.destroy ? <span>—</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr><TableListEmpty colSpan={5}>Nenhum ponto de referência encontrado.</TableListEmpty></tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    summary={`${locations.total} ponto(s) de referência`}
                    currentPage={locations.current_page}
                    pageCount={locations.last_page}
                    disabled={busy}
                    paginationLabel="Paginação de pontos de referência"
                    onSelect={(page) => visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={locationToDelete !== null}
                title="Excluir Ponto de Referência"
                message={<>O ponto <strong>{locationToDelete?.name}</strong> será excluído permanentemente.</>}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo ponto..."
                confirmTone="dark-red"
                onClose={() => { if (!busy) { setLocationToDelete(null); setFailure(""); } }}
                onConfirm={remove}
            />
        </LocationPage>
    );
}
