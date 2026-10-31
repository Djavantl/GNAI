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
import InstitutionPage from "./InstitutionPage";
import type { InstitutionFilters, InstitutionPaginator, InstitutionRecord } from "./Institution.types";
import "./Institutions.css";

type Props = {
    institutions: InstitutionPaginator;
    filters: Partial<InstitutionFilters>;
    can: { create: boolean; show: boolean; destroy: boolean };
};

const base = "/inclusive-radar/institutions";
const emptyFilters: InstitutionFilters = { name: "", location: "", is_active: "" };
const columns: TableListColumn[] = [
    { id: "name", label: "Nome", width: "39%", minWidth: 140, fixed: "start" },
    { id: "location", label: "Localização", width: "28%", minWidth: 110 },
    { id: "status", label: "Status", width: "15%", minWidth: 85 },
    { id: "shortName", label: "Sigla", width: "16%", defaultVisible: false },
    { id: "district", label: "Bairro", width: "16%", defaultVisible: false },
    { id: "address", label: "Endereço", width: "16%", defaultVisible: false },
    { id: "zoom", label: "Zoom padrão", width: "16%", defaultVisible: false },
    { id: "latitude", label: "Latitude", width: "16%", defaultVisible: false },
    { id: "longitude", label: "Longitude", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "18%", minWidth: 86, fixed: "end" },
];

export default function InclusionInstitutionsIndexPage({ institutions, filters: initialFilters, can }: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: base,
        failureText: "Não foi possível atualizar as instituições. Tente novamente.",
    });
    const removal = useTableListAction<InstitutionRecord>({
        failureText: "Não foi possível excluir a instituição. Tente novamente.",
    });
    const initialValues = useMemo(() => ({ ...emptyFilters, ...initialFilters }), [initialFilters]);
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (filters) => list.visit(filters),
    });

    return (
        <InstitutionPage
            title="Instituições Base"
            subtitle="Gerencie os locais centrais onde o radar de acessibilidade opera."
            actions={can.create ? <TableListCreateButton href={`${base}/create`} ariaLabel="Cadastrar instituição" /> : undefined}
        >
            <TableListCard aria-label="Listagem de instituições" aria-busy={list.loading} loading={list.loading} loadingLabel="Atualizando instituições">
                <FormLayout onSubmit={(event) => { event.preventDefault(); submit(); }}>
                    <div className="institutionIndex__filters">
                        <FormField id="institution-filter-name" label="Nome da Instituição" error={pageErrors.name}>
                            <TextInput
                                id="institution-filter-name" name="name" value={values.name} maxLength={255}
                                placeholder="Filtrar por nome..." invalid={!!pageErrors.name}
                                aria-describedby={pageErrors.name ? "institution-filter-name-error" : undefined}
                                onChange={(event) => update("name", event.target.value)}
                            />
                        </FormField>
                        <FormField id="institution-filter-location" label="Cidade ou Estado" error={pageErrors.location}>
                            <TextInput
                                id="institution-filter-location" name="location" value={values.location} maxLength={255}
                                placeholder="Filtrar por cidade ou estado..." invalid={!!pageErrors.location}
                                aria-describedby={pageErrors.location ? "institution-filter-location-error" : undefined}
                                onChange={(event) => update("location", event.target.value)}
                            />
                        </FormField>
                        <FormField id="institution-filter-status" label="Status" error={pageErrors.is_active}>
                            <SelectField
                                id="institution-filter-status" name="is_active" label="Status" placeholder="Todos"
                                value={values.is_active}
                                options={[{ value: "1", label: "Ativo" }, { value: "0", label: "Inativo" }]}
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "institution-filter-status-error" : undefined}
                                onChange={(event) => update("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de instituições">
                    <TableListTable
                        className="institutionIndex__table"
                        columns={columns}
                        page={institutions}
                    >
                        {institutions.data.length ? institutions.data.map((institution) => (
                            <tr key={institution.id}>
                                <td>{institution.name}</td>
                                <td>{institution.city} - {institution.state}</td>
                                <td>
                                    <TableLabel tone={institution.is_active ? "success" : "inverse"}>
                                        {institution.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
                                <td>{institution.short_name || "Não informado"}</td>
                                <td>{institution.district || "Não informado"}</td>
                                <td>{formatTableListText(institution.address)}</td>
                                <td>{institution.default_zoom}</td>
                                <td>{institution.latitude ?? "Não informado"}</td>
                                <td>{institution.longitude ?? "Não informado"}</td>
                                <td>
                                    <TableActions>
                                        {can.show ? (
                                            <Button
                                                href={`${base}/${institution.id}`}
                                                size="sm" tone="dark-blue" iconOnly leftIconClass="fa fa-eye"
                                                aria-label={`Visualizar ${institution.name}`}
                                            />
                                        ) : null}
                                        {can.destroy ? (
                                            <Button
                                                tone="dark-red" size="sm" iconOnly leftIconClass="fa fa-trash-o"
                                                aria-label={`Excluir ${institution.name}`}
                                                onClick={() => {
                                                    cancelScheduled();
                                                    removal.open(institution);
                                                }}
                                            />
                                        ) : null}
                                        {!can.show && !can.destroy ? <span>Sem ações</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr><TableListEmpty colSpan={columns.length}>Nenhuma instituição cadastrada.</TableListEmpty></tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={institutions}
                    disabled={list.loading}
                    paginationLabel="Paginação de instituições"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Instituição"
                message={<>A instituição <strong>{removal.target?.name}</strong> e seus dados relacionados serão excluídos permanentemente.</>}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo instituição..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (removal.target) removal.submit("delete", `${base}/${removal.target.id}`);
                }}
            />
        </InstitutionPage>
    );
}
