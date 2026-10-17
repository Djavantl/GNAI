import { router, usePage } from "@inertiajs/react";
import { useMemo, useRef, useState } from "react";
import Button from "@/components/UI/Button/Button";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    TableListCard,
    TableListClearButton,
    TableListCreateButton,
    TableListEmpty,
    TableListFilterActions,
    TableListFooter,
    TableListProgress,
    TableListScroll,
    TableListTable,
} from "@/components/Tables/TableList/TableList";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import TextInput from "@/components/Forms/TextInput/TextInput";
import SelectField from "@/components/Forms/SelectField/SelectField";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import InlineAlert from "@/components/UI/InlineAlert/InlineAlert";
import useAutoListFilters, {
    compactListFilters,
} from "@/hooks/useAutoListFilters";
import AssistiveTechnologyPage from "./AssistiveTechnologyPage";
import "./Index.css";

type Filters = {
    name: string;
    is_digital: string;
    is_active: string;
    available: string;
};
type Technology = {
    id: number;
    name: string;
    is_digital: boolean;
    is_active: boolean;
    quantity: number | null;
    quantity_available: number | null;
};
type Props = {
    assistiveTechnologies: {
        data: Technology[];
        current_page: number;
        last_page: number;
        total: number;
        from: number | null;
        to: number | null;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
    filters: Partial<Filters>;
    can: { create: boolean; show: boolean; destroy: boolean };
};
const base = "/inclusive-radar/assistive-technologies";
const emptyFilters: Filters = {
    name: "",
    is_digital: "",
    is_active: "",
    available: "",
};
const selects = [
    { name: "is_digital", label: "Natureza", yes: "Digital", no: "Físico" },
    { name: "is_active", label: "Status", yes: "Ativo", no: "Inativo" },
    {
        name: "available",
        label: "Disponibilidade",
        yes: "Disponível",
        no: "Indisponível",
    },
] as const;

export default function InclusiveRadarAssistiveTechnologiesIndexPage({
    assistiveTechnologies: records,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const { errors } = usePage().props;
    const [busy, setBusy] = useState(false);
    const [failure, setFailure] = useState("");
    const [technologyToDelete, setTechnologyToDelete] =
        useState<Technology | null>(null);
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

    function visit(values: Filters, url = base): void {
        if (pending.current) {
            return;
        }

        pending.current = true;
        setBusy(true);
        setFailure("");
        router.get(
            url,
            url === base ? compactListFilters(values) : {},
            {
                preserveState: true,
                preserveScroll: true,
                onError: () =>
                    setFailure(
                        "Não foi possível filtrar as tecnologias. Confira os campos e tente novamente.",
                    ),
                onHttpException: () => {
                    setFailure(
                        "Não foi possível concluir a operação. Tente novamente.",
                    );
                    return false;
                },
                onNetworkError: () => {
                    setFailure(
                        "Falha de conexão. Confira sua conexão e tente novamente.",
                    );
                    return false;
                },
                onFinish: () => {
                    pending.current = false;
                    setBusy(false);
                },
            },
        );
    }
    function remove(): void {
        if (pending.current || !technologyToDelete) {
            return;
        }

        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.delete(`${base}/${technologyToDelete.id}`, {
            preserveScroll: true,
            onError: () =>
                setFailure(
                    "Não foi possível excluir a tecnologia. Tente novamente.",
                ),
            onHttpException: () => {
                setFailure(
                    "Não foi possível concluir a operação. Tente novamente.",
                );
                return false;
            },
            onNetworkError: () => {
                setFailure(
                    "Falha de conexão. Confira sua conexão e tente novamente.",
                );
                return false;
            },
            onSuccess: () => setTechnologyToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    return (
        <AssistiveTechnologyPage
            className="technologyIndex"
            title="Tecnologias Assistivas"
            subtitle="Gerenciamento de periféricos, softwares e equipamentos de acessibilidade."
            actions={
                can.create ? (
                    <TableListCreateButton
                        href={`${base}/create`}
                        ariaLabel="Cadastrar tecnologia assistiva"
                    />
                ) : undefined
            }
        >
            <TableListCard
                aria-label="Listagem de tecnologias assistivas"
                aria-busy={busy}
            >
                <FormLayout
                    className="technologyIndex__filters"
                    onSubmit={(event) => {
                        event.preventDefault();
                        submitFilters();
                    }}
                >
                    <FormField
                        id="technology-name"
                        label="Nome"
                        error={errors.name}
                    >
                        <TextInput
                            id="technology-name"
                            name="name"
                            value={filters.name}
                            maxLength={255}
                            placeholder="Filtrar por nome..."
                            disabled={busy}
                            onChange={(event) =>
                                updateFilter("name", event.target.value)
                            }
                            invalid={!!errors.name}
                            aria-describedby={
                                errors.name
                                    ? "technology-name-error"
                                    : undefined
                            }
                        />
                    </FormField>
                    {selects.map((field) => (
                        <FormField
                            key={field.name}
                            id={field.name}
                            label={field.label}
                            error={errors[field.name]}
                        >
                            <SelectField
                                id={field.name}
                                name={field.name}
                                label={field.label}
                                placeholder="Todos"
                                options={[
                                    { value: "1", label: field.yes },
                                    { value: "0", label: field.no },
                                ]}
                                value={filters[field.name]}
                                disabled={busy}
                                onChange={(event) =>
                                    updateFilter(field.name, event.target.value)
                                }
                                aria-invalid={!!errors[field.name]}
                                aria-describedby={
                                    errors[field.name]
                                        ? `${field.name}-error`
                                        : undefined
                                }
                            />
                        </FormField>
                    ))}
                    <TableListFilterActions>
                        <TableListClearButton
                            disabled={busy}
                            onClick={clearFilters}
                        />
                    </TableListFilterActions>
                </FormLayout>
                <TableListProgress>
                    {busy
                        ? "Processando…"
                        : `${records.total} tecnologia(s) encontrada(s)`}
                </TableListProgress>
                {failure && !technologyToDelete ? (
                    <div role="alert">
                        <InlineAlert variant="danger">{failure}</InlineAlert>
                    </div>
                ) : null}
                <TableListScroll aria-label="Tabela de tecnologias assistivas">
                    <TableListTable className="technologyIndex__table">
                        <thead>
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">Natureza</th>
                                <th scope="col">Estoque</th>
                                <th scope="col">Status</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            {records.data.map((technology) => {
                                const exhausted =
                                    !technology.is_digital &&
                                    (technology.quantity_available ?? 0) <= 0;
                                return (
                                    <tr key={technology.id}>
                                        <td>{technology.name}</td>
                                        <td>
                                            {technology.is_digital
                                                ? "Digital"
                                                : "Físico"}
                                        </td>
                                        <td>
                                            {technology.is_digital
                                                ? "Ilimitado"
                                                : `${technology.quantity_available ?? 0} / ${technology.quantity ?? 0}`}
                                        </td>
                                        <td>
                                            <TableLabel
                                                tone={
                                                    exhausted
                                                        ? "danger"
                                                        : technology.is_active
                                                          ? "success"
                                                          : "inverse"
                                                }
                                            >
                                                {exhausted
                                                    ? "Esgotado"
                                                    : technology.is_active
                                                      ? "Ativo"
                                                      : "Inativo"}
                                            </TableLabel>
                                        </td>
                                        <td>
                                            <TableActions>
                                                {can.show && (
                                                    <Button
                                                        href={`${base}/${technology.id}`}
                                                        size="sm"
                                                        tone="dark-blue"
                                                        iconOnly
                                                        leftIconClass="fa fa-eye"
                                                        aria-label={`Ver ${technology.name}`}
                                                    />
                                                )}
                                                {can.destroy && (
                                                    <Button
                                                        size="sm"
                                                        tone="dark-red"
                                                        iconOnly
                                                        leftIconClass="fa fa-trash-o"
                                                        disabled={busy}
                                                        aria-label={`Excluir ${technology.name}`}
                                                        onClick={() => {
                                                            setFailure("");
                                                            setTechnologyToDelete(
                                                                technology,
                                                            );
                                                        }}
                                                    />
                                                )}
                                                {!can.show && !can.destroy && (
                                                    <span>Nenhuma ação</span>
                                                )}
                                            </TableActions>
                                        </td>
                                    </tr>
                                );
                            })}
                            {!records.data.length && (
                                <tr>
                                    <TableListEmpty colSpan={5}>
                                        Nenhuma tecnologia cadastrada.
                                    </TableListEmpty>
                                </tr>
                            )}
                        </tbody>
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    summary={`${records.from ?? 0}–${records.to ?? 0} de ${records.total}`}
                    currentPage={records.current_page}
                    pageCount={records.last_page}
                    disabled={busy}
                    paginationLabel="Páginas da listagem"
                    onSelect={(page) => {
                        cancelScheduled();
                        const params = new URLSearchParams(
                            window.location.search,
                        );
                        params.set("page", String(page));
                        visit(filters, `${base}?${params.toString()}`);
                    }}
                />
            </TableListCard>
            <ConfirmActionModal
                open={technologyToDelete !== null}
                title="Excluir Tecnologia Assistiva"
                message={
                    <>
                        Excluir a tecnologia{" "}
                        <strong>{technologyToDelete?.name}</strong>?
                    </>
                }
                error={failure || undefined}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo tecnologia..."
                confirmTone="dark-red"
                onClose={() => {
                    setFailure("");
                    setTechnologyToDelete(null);
                }}
                onConfirm={remove}
            />
        </AssistiveTechnologyPage>
    );
}
