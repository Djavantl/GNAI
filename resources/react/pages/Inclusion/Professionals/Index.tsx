import { usePage } from "@inertiajs/react";
import { useMemo } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    formatTableListDate,
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
import ProfessionalPage from "./ProfessionalPage";
import { professionalsBase, professionalShowUrl } from "./Professionals.utils";
import type {
    ProfessionalFilters,
    ProfessionalIndexProps,
    ProfessionalListRecord,
} from "./Professional.types";

const emptyFilters: ProfessionalFilters = {
    name: "",
    email: "",
    registration: "",
    position: "",
    status: "",
};

const columns: TableListColumn[] = [
    { id: "name", label: "Nome", width: "32%", minWidth: 140, fixed: "start" },
    { id: "email", label: "E-mail", width: "26%", minWidth: 110 },
    { id: "position", label: "Cargo", width: "17%", minWidth: 90 },
    { id: "status", label: "Status", width: "11%", minWidth: 72 },
    { id: "registration", label: "Matrícula", width: "16%", defaultVisible: false },
    { id: "phone", label: "Telefone", width: "16%", defaultVisible: false },
    { id: "entry", label: "Ingresso", width: "16%", defaultVisible: false },
    { id: "website", label: "Aparece no site", width: "14%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "14%", minWidth: 86, fixed: "end" },
];

function filterValues(filters: Partial<ProfessionalFilters>): ProfessionalFilters {
    return {
        name: String(filters.name ?? ""),
        email: String(filters.email ?? ""),
        registration: String(filters.registration ?? ""),
        position: String(filters.position ?? ""),
        status: String(filters.status ?? ""),
    };
}

export default function InclusionProfessionalsIndexPage({
    professionals,
    positions,
    professionalStatuses,
    professionalStatusColors,
    filters: initialFilters,
    can,
}: ProfessionalIndexProps): React.JSX.Element {
    const errors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: professionalsBase,
        failureText: "Não foi possível atualizar a listagem de profissionais. Tente novamente.",
    });
    const removal = useTableListAction<ProfessionalListRecord>({
        failureText: "Não foi possível excluir o profissional. Tente novamente.",
    });
    const initialValues = useMemo(() => filterValues(initialFilters), [initialFilters]);
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (next) => list.visit(next),
    });
    const positionOptions = positions.map((position) => ({
        value: String(position.id),
        label: position.name,
    }));
    const statusOptions = Object.entries(professionalStatuses).map(([value, label]) => ({
        value,
        label,
    }));

    function textFilter(
        name: "name" | "email" | "registration",
        label: string,
        placeholder: string,
        maxLength: number,
    ): React.JSX.Element {
        const id = `professional-filter-${name}`;

        return (
            <FormField
                id={id}
                label={label}
                error={errors[name]}
            >
                <TextInput
                    id={id}
                    name={name}
                    value={values[name]}
                    maxLength={maxLength}
                    placeholder={placeholder}
                    invalid={!!errors[name]}
                    aria-describedby={errors[name] ? `${id}-error` : undefined}
                    onChange={(event) => update(name, event.target.value)}
                />
            </FormField>
        );
    }

    function selectFilter(
        name: "position" | "status",
        label: string,
        options: Array<{ value: string; label: string }>,
    ): React.JSX.Element {
        const id = `professional-filter-${name}`;

        return (
            <FormField
                id={id}
                label={label}
                error={errors[name]}
            >
                <SelectField
                    id={id}
                    name={name}
                    label={label}
                    placeholder="Todos"
                    value={values[name]}
                    options={options}
                    aria-invalid={!!errors[name]}
                    aria-describedby={errors[name] ? `${id}-error` : undefined}
                    onChange={(event) => update(name, event.target.value)}
                />
            </FormField>
        );
    }

    return (
        <ProfessionalPage
            title="Profissionais"
            subtitle="Cadastre profissionais ou tutores de pares no sistema. Embora esta seção seja denominada “Profissionais”, também é possível cadastrar e gerenciar tutores de pares, utilizando a mesma estrutura de dados e funcionalidades disponíveis."
            actions={
                can.create ? (
                    <TableListCreateButton
                        href={`${professionalsBase}/create`}
                        ariaLabel="Cadastrar profissional"
                    />
                ) : undefined
            }
        >
            <TableListCard
                aria-label="Listagem de profissionais"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando profissionais"
            >
                <FormLayout
                    aria-label="Filtrar profissionais"
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="professionalIndex__filters">
                        {textFilter("name", "Nome do profissional", "Nome do profissional...", 255)}
                        {selectFilter("position", "Cargo", positionOptions)}
                        {selectFilter("status", "Status", statusOptions)}
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
                        </TableListFilterActions>
                    </div>
                    <TableListMoreFilters active={Boolean(values.email || values.registration)}>
                        <div className="professionalIndex__filters professionalIndex__filters--secondary">
                            {textFilter("email", "E-mail", "E-mail...", 255)}
                            {textFilter("registration", "Matrícula", "Matrícula...", 50)}
                        </div>
                    </TableListMoreFilters>
                </FormLayout>
                <TableListScroll aria-label="Tabela de profissionais do suporte educacional">
                    <TableListTable
                        className="professionalIndex__table"
                        columns={columns}
                        page={professionals}
                    >
                        {professionals.data.length ? (
                            professionals.data.map((professional) => (
                                <tr key={professional.id}>
                                    <td>
                                        <div className="professionalIndex__identity">
                                            {professional.person.photo_url ? (
                                                <img
                                                    src={professional.person.photo_url}
                                                    alt={`Foto de ${professional.person.name}`}
                                                    className="professionalIndex__avatar"
                                                />
                                            ) : null}
                                            <strong>{professional.person.name}</strong>
                                        </div>
                                    </td>
                                    <td>{professional.person.email || "Não informado"}</td>
                                    <td>{professional.position?.name || "Não informado"}</td>
                                    <td>
                                        {professional.status ? (
                                            <TableLabel tone={professionalStatusColors[professional.status] ?? "inverse"}>
                                                {professionalStatuses[professional.status] ?? professional.status}
                                            </TableLabel>
                                        ) : (
                                            "Não informado"
                                        )}
                                    </td>
                                    <td>{professional.registration || "Não informado"}</td>
                                    <td>{professional.person.phone || "Não informado"}</td>
                                    <td>{formatTableListDate(professional.entry_date)}</td>
                                    <td>{professional.show_on_website ? "Sim" : "Não"}</td>
                                    <td>
                                        {can.view || can.delete ? (
                                            <TableActions>
                                                {can.view ? (
                                                    <Button
                                                        href={professionalShowUrl(professional.id)}
                                                        size="sm"
                                                        tone="dark-blue"
                                                        iconOnly
                                                        leftIconClass="fa fa-eye"
                                                        aria-label={`Visualizar prontuário de ${professional.person.name}`}
                                                    />
                                                ) : null}
                                                {can.delete ? (
                                                    <Button
                                                        tone="dark-red"
                                                        size="sm"
                                                        iconOnly
                                                        leftIconClass="fa fa-trash-o"
                                                        aria-label={`Excluir profissional ${professional.person.name} do sistema`}
                                                        disabled={removal.processing}
                                                        onClick={() => {
                                                            cancelScheduled();
                                                            removal.open(professional);
                                                        }}
                                                    />
                                                ) : null}
                                            </TableActions>
                                        ) : (
                                            "Nenhuma ação"
                                        )}
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    Nenhum profissional encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={professionals}
                    disabled={list.loading}
                    paginationLabel="Paginação de profissionais"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Profissional"
                message={
                    <>
                        Deseja remover o profissional <strong>{removal.target?.person.name}</strong>?
                    </>
                }
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo profissional..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (!removal.target) return;
                    cancelScheduled();
                    list.cancel();
                    removal.submit("delete", `${professionalsBase}/${removal.target.id}`);
                }}
            />
        </ProfessionalPage>
    );
}
