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
    { id: "actions", label: "Ações", width: "18%", minWidth: 86, fixed: "end" },
];

export default function InclusiveRadarInstitutionsIndexPage({ institutions, filters: initialFilters, can }: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const pending = useRef(false);
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [failure, setFailure] = useState("");
    const [institutionToDelete, setInstitutionToDelete] = useState<InstitutionRecord | null>(null);
    const initialValues = useMemo(() => ({ ...emptyFilters, ...initialFilters }), [initialFilters]);
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (filters) => visit(filters),
    });

    useEffect(() => {
        if (failure) notify({ tone: "error", title: "Não foi possível concluir", text: failure });
    }, [failure]);

    function visit(filters: InstitutionFilters, page = 1): void {
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
                setFailure("Não foi possível atualizar as instituições. Tente novamente.");
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
        if (pending.current || !institutionToDelete) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.delete(`${base}/${institutionToDelete.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir a instituição. Tente novamente.");
                return false;
            },
            onSuccess: () => setInstitutionToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    return (
        <InstitutionPage
            title="Instituições Base"
            subtitle="Gerencie os locais centrais onde o radar de acessibilidade opera."
            actions={can.create ? <TableListCreateButton href={`${base}/create`} ariaLabel="Cadastrar instituição" /> : undefined}
        >
            <TableListCard aria-label="Listagem de instituições" aria-busy={busy} loading={listLoading} loadingLabel="Atualizando instituições">
                <FormLayout onSubmit={(event) => { event.preventDefault(); submit(); }}>
                    <div className="institutionIndex__filters">
                        <FormField id="institution-filter-name" label="Nome da Instituição" error={pageErrors.name}>
                            <TextInput
                                id="institution-filter-name" name="name" value={values.name} maxLength={255}
                                placeholder="Filtrar por nome..." disabled={busy} invalid={!!pageErrors.name}
                                aria-describedby={pageErrors.name ? "institution-filter-name-error" : undefined}
                                onChange={(event) => update("name", event.target.value)}
                            />
                        </FormField>
                        <FormField id="institution-filter-location" label="Cidade ou Estado" error={pageErrors.location}>
                            <TextInput
                                id="institution-filter-location" name="location" value={values.location} maxLength={255}
                                placeholder="Filtrar por cidade ou estado..." disabled={busy} invalid={!!pageErrors.location}
                                aria-describedby={pageErrors.location ? "institution-filter-location-error" : undefined}
                                onChange={(event) => update("location", event.target.value)}
                            />
                        </FormField>
                        <FormField id="institution-filter-status" label="Status" error={pageErrors.is_active}>
                            <SelectField
                                id="institution-filter-status" name="is_active" label="Status" placeholder="Todos"
                                value={values.is_active}
                                options={[{ value: "1", label: "Ativo" }, { value: "0", label: "Inativo" }]}
                                disabled={busy} aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "institution-filter-status-error" : undefined}
                                onChange={(event) => update("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton disabled={busy} onClick={clear} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de instituições">
                    <TableListTable
                        className="institutionIndex__table"
                        columns={columns}
                        summary={`${institutions.from ?? 0}–${institutions.to ?? 0} de ${institutions.total} registros`}
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
                                                    setFailure("");
                                                    setInstitutionToDelete(institution);
                                                }}
                                            />
                                        ) : null}
                                        {!can.show && !can.destroy ? <span>—</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr><TableListEmpty colSpan={4}>Nenhuma instituição cadastrada.</TableListEmpty></tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    summary={`${institutions.total} instituição(ões)`}
                    currentPage={institutions.current_page}
                    pageCount={institutions.last_page}
                    disabled={busy}
                    paginationLabel="Paginação de instituições"
                    onSelect={(page) => visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={institutionToDelete !== null}
                title="Excluir Instituição"
                message={<>A instituição <strong>{institutionToDelete?.name}</strong> e seus dados relacionados serão excluídos permanentemente.</>}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo instituição..."
                confirmTone="dark-red"
                onClose={() => { if (!busy) { setInstitutionToDelete(null); setFailure(""); } }}
                onConfirm={remove}
            />
        </InstitutionPage>
    );
}
