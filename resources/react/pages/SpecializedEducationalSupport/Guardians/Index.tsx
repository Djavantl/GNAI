import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useRef, useState } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import StudentRecordReturnButton from "@/components/Students/StudentRecordReturnButton";
import { aeeBase, studentsBase } from "@/components/Students/Students.utils";
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
import useAutoListFilters, { compactListFilters } from "@/hooks/useAutoListFilters";
import GuardianPage from "./GuardianPage";
import type {
    GuardianFilters,
    GuardianIndexProps,
    GuardianRecord,
} from "./Guardian.types";

const emptyFilters: GuardianFilters = {
    name: "",
    email: "",
    relationship: "",
};

const columns: TableListColumn[] = [
    { id: "name", label: "Responsável", width: "30%", minWidth: 140, fixed: "start" },
    { id: "document", label: "CPF", width: "16%", minWidth: 90 },
    { id: "relationship", label: "Vínculo", width: "14%", minWidth: 80 },
    { id: "contact", label: "Contato", width: "26%", minWidth: 110 },
    { id: "actions", label: "Ações", width: "14%", minWidth: 86, fixed: "end" },
];

function filterValues(filters: Partial<GuardianFilters>): GuardianFilters {
    return {
        name: String(filters.name ?? ""),
        email: String(filters.email ?? ""),
        relationship: String(filters.relationship ?? ""),
    };
}

export default function SpecializedEducationalSupportGuardiansIndexPage({
    student,
    guardians,
    relationships,
    filters: initialFilters,
    can,
}: GuardianIndexProps): React.JSX.Element {
    const errors = usePage().props.errors as Record<string, string | undefined>;
    const cancelVisit = useRef<(() => void) | undefined>(undefined);
    const deleting = useRef(false);
    const [listLoading, setListLoading] = useState(false);
    const [busy, setBusy] = useState(false);
    const [guardianToDelete, setGuardianToDelete] = useState<GuardianRecord | null>(null);
    const initialValues = useMemo(() => filterValues(initialFilters), [initialFilters]);
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (next) => visit(next),
    });
    const guardiansBase = `${studentsBase}/${student.id}/guardians`;
    const relationshipOptions = Object.entries(relationships).map(([value, label]) => ({
        value,
        label,
    }));

    useEffect(
        () => () => {
            cancelVisit.current?.();
        },
        [],
    );

    function failure(text: string): void {
        notify({
            tone: "error",
            title: "Não foi possível concluir",
            text,
        });
    }

    function visit(next: GuardianFilters, page = 1): void {
        if (busy) return;
        cancelVisit.current?.();
        router.get(
            guardiansBase,
            {
                ...compactListFilters(next),
                ...(page > 1 ? { page } : {}),
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onCancelToken: (token) => {
                    cancelVisit.current = () => token.cancel();
                },
                onStart: () => setListLoading(true),
                onNetworkError: () => {
                    failure("Falha de conexão. Confira sua conexão e tente novamente.");
                    return false;
                },
                onHttpException: () => {
                    failure("Não foi possível atualizar a listagem de responsáveis. Tente novamente.");
                    return false;
                },
                onFinish: () => {
                    cancelVisit.current = undefined;
                    setListLoading(false);
                },
            },
        );
    }

    function remove(): void {
        if (deleting.current || !guardianToDelete) return;
        cancelScheduled();
        cancelVisit.current?.();
        deleting.current = true;
        setBusy(true);
        router.delete(`${guardiansBase}/${guardianToDelete.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                failure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                failure("Não foi possível excluir o responsável. Tente novamente.");
                return false;
            },
            onSuccess: () => setGuardianToDelete(null),
            onFinish: () => {
                deleting.current = false;
                setBusy(false);
            },
        });
    }

    function textFilter(
        name: "name" | "email",
        label: string,
        placeholder: string,
    ): React.JSX.Element {
        const id = `guardian-filter-${name}`;

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
                    maxLength={255}
                    placeholder={placeholder}
                    invalid={!!errors[name]}
                    aria-describedby={errors[name] ? `${id}-error` : undefined}
                    onChange={(event) => update(name, event.target.value)}
                />
            </FormField>
        );
    }

    return (
        <GuardianPage
            title={`Responsáveis — ${student.person.name}`}
            subtitle="Gerenciamento de vínculos familiares e contatos de emergência."
            student={student}
            actions={
                <>
                    <StudentRecordReturnButton
                        studentId={student.id}
                        tab="guardians"
                    />
                    {can.create ? (
                        <TableListCreateButton
                            href={`${guardiansBase}/create`}
                            ariaLabel="Cadastrar responsável do aluno"
                        />
                    ) : null}
                </>
            }
        >
            <TableListCard
                aria-label="Listagem de responsáveis do aluno"
                aria-busy={busy}
                loading={listLoading}
                loadingLabel="Atualizando responsáveis"
            >
                <FormLayout
                    aria-label="Filtrar responsáveis"
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="guardianIndex__filters">
                        {textFilter("name", "Nome do responsável", "Nome do responsável...")}
                        {textFilter("email", "E-mail", "E-mail...")}
                        <FormField
                            id="guardian-filter-relationship"
                            label="Parentesco"
                            error={errors.relationship}
                        >
                            <SelectField
                                id="guardian-filter-relationship"
                                name="relationship"
                                label="Parentesco"
                                placeholder="Todos"
                                value={values.relationship}
                                options={relationshipOptions}
                                aria-invalid={!!errors.relationship}
                                aria-describedby={errors.relationship ? "guardian-filter-relationship-error" : undefined}
                                onChange={(event) => update("relationship", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton
                                disabled={listLoading}
                                onClick={clear}
                            />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de responsáveis do aluno">
                    <TableListTable
                        className="guardianIndex__table"
                        columns={columns}
                        page={guardians}
                    >
                        {guardians.data.length ? (
                            guardians.data.map((guardian) => (
                                <tr key={guardian.id}>
                                    <td>
                                        <div className="guardianIndex__identity">
                                            {guardian.person.photo_url ? (
                                                <img
                                                    src={guardian.person.photo_url}
                                                    alt={`Foto de ${guardian.person.name}`}
                                                    className="guardianIndex__avatar"
                                                />
                                            ) : null}
                                            <strong>{guardian.person.name}</strong>
                                        </div>
                                    </td>
                                    <td>{guardian.person.document || "—"}</td>
                                    <td>
                                        {guardian.relationship
                                            ? relationships[guardian.relationship] ?? guardian.relationship
                                            : "—"}
                                    </td>
                                    <td>{guardian.person.email || guardian.person.phone || "—"}</td>
                                    <td>
                                        {can.view || can.delete ? (
                                            <TableActions>
                                                {can.view ? (
                                                    <Button
                                                        href={`${aeeBase}/guardian/${guardian.id}/show`}
                                                        size="sm"
                                                        tone="dark-blue"
                                                        iconOnly
                                                        leftIconClass="fa fa-eye"
                                                        aria-label={`Visualizar responsável ${guardian.person.name}`}
                                                    />
                                                ) : null}
                                                {can.delete ? (
                                                    <Button
                                                        tone="dark-red"
                                                        size="sm"
                                                        iconOnly
                                                        leftIconClass="fa fa-trash-o"
                                                        aria-label={`Excluir responsável ${guardian.person.name}`}
                                                        disabled={busy}
                                                        onClick={() => {
                                                            cancelScheduled();
                                                            setGuardianToDelete(guardian);
                                                        }}
                                                    />
                                                ) : null}
                                            </TableActions>
                                        ) : (
                                            "Sem ações"
                                        )}
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <TableListEmpty colSpan={5}>
                                    Nenhum responsável encontrado para este aluno.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={guardians}
                    disabled={listLoading || busy}
                    paginationLabel="Paginação de responsáveis"
                    onSelect={(page) => visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={guardianToDelete !== null}
                title="Excluir Responsável"
                message={
                    <>
                        Deseja remover o vínculo de responsabilidade de{" "}
                        <strong>{guardianToDelete?.person.name}</strong>? Esta ação não pode ser desfeita.
                    </>
                }
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo responsável..."
                confirmTone="dark-red"
                onClose={() => {
                    if (!busy) setGuardianToDelete(null);
                }}
                onConfirm={remove}
            />
        </GuardianPage>
    );
}
