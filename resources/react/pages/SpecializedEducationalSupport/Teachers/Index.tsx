import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useRef, useState } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
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
import useAutoListFilters, { compactListFilters } from "@/hooks/useAutoListFilters";
import TeacherPage from "./TeacherPage";
import { teachersBase, teacherShowUrl } from "./Teachers.utils";
import type {
    TeacherFilters,
    TeacherIndexProps,
    TeacherRecord,
} from "./Teacher.types";

const emptyFilters: TeacherFilters = {
    name: "",
    email: "",
    registration: "",
};

const columns: TableListColumn[] = [
    { id: "name", label: "Nome", width: "38%", minWidth: 140, fixed: "start" },
    { id: "email", label: "E-mail", width: "30%", minWidth: 110 },
    { id: "registration", label: "Matrícula", width: "18%", minWidth: 90 },
    { id: "actions", label: "Ações", width: "14%", minWidth: 86, fixed: "end" },
];

const textFilters = [
    {
        name: "name",
        label: "Nome do professor",
        placeholder: "Nome do professor...",
        maxLength: 255,
    },
    {
        name: "email",
        label: "E-mail",
        placeholder: "E-mail...",
        maxLength: 255,
    },
    {
        name: "registration",
        label: "Matrícula",
        placeholder: "Matrícula...",
        maxLength: 50,
    },
] as const;

function filterValues(filters: Partial<TeacherFilters>): TeacherFilters {
    return {
        name: String(filters.name ?? ""),
        email: String(filters.email ?? ""),
        registration: String(filters.registration ?? ""),
    };
}

export default function SpecializedEducationalSupportTeachersIndexPage({
    teachers,
    filters: initialFilters,
    can,
}: TeacherIndexProps): React.JSX.Element {
    const errors = usePage().props.errors as Record<string, string | undefined>;
    const cancelVisit = useRef<(() => void) | undefined>(undefined);
    const deleting = useRef(false);
    const [listLoading, setListLoading] = useState(false);
    const [busy, setBusy] = useState(false);
    const [teacherToDelete, setTeacherToDelete] = useState<TeacherRecord | null>(null);
    const initialValues = useMemo(() => filterValues(initialFilters), [initialFilters]);
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (next) => visit(next),
    });

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

    function visit(next: TeacherFilters, page = 1): void {
        if (busy) return;
        cancelVisit.current?.();
        router.get(
            teachersBase,
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
                    failure("Não foi possível atualizar a listagem de professores. Tente novamente.");
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
        if (deleting.current || !teacherToDelete) return;
        cancelScheduled();
        cancelVisit.current?.();
        deleting.current = true;
        setBusy(true);
        router.delete(`${teachersBase}/${teacherToDelete.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                failure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                failure("Não foi possível excluir o professor. Tente novamente.");
                return false;
            },
            onSuccess: () => setTeacherToDelete(null),
            onFinish: () => {
                deleting.current = false;
                setBusy(false);
            },
        });
    }

    const headerActions = can.update || can.create ? (
        <>
            {can.update ? (
                <Button
                    href={`${teachersBase}/permissions`}
                    tone="default"
                    labelIconClass="fa fa-shield"
                    title="Permissões globais dos professores"
                    aria-label="Permissões globais dos professores"
                >
                    Permissões
                </Button>
            ) : null}
            {can.create ? (
                <TableListCreateButton
                    href={`${teachersBase}/create`}
                    ariaLabel="Cadastrar professor"
                />
            ) : null}
        </>
    ) : undefined;

    return (
        <TeacherPage
            title="Professores"
            subtitle="Gerencie o corpo docente e suas atribuições de disciplinas."
            actions={headerActions}
        >
            <TableListCard
                aria-label="Listagem de professores"
                aria-busy={busy}
                loading={listLoading}
                loadingLabel="Atualizando professores"
            >
                <FormLayout
                    aria-label="Filtrar professores"
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="teacherIndex__filters">
                        {textFilters.map((filter) => {
                            const id = `teacher-filter-${filter.name}`;

                            return (
                                <FormField
                                    key={filter.name}
                                    id={id}
                                    label={filter.label}
                                    error={errors[filter.name]}
                                >
                                    <TextInput
                                        id={id}
                                        name={filter.name}
                                        value={values[filter.name]}
                                        maxLength={filter.maxLength}
                                        placeholder={filter.placeholder}
                                        invalid={!!errors[filter.name]}
                                        aria-describedby={errors[filter.name] ? `${id}-error` : undefined}
                                        onChange={(event) => update(filter.name, event.target.value)}
                                    />
                                </FormField>
                            );
                        })}
                        <TableListFilterActions>
                            <TableListClearButton
                                disabled={listLoading}
                                onClick={clear}
                            />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de professores">
                    <TableListTable
                        className="teacherIndex__table"
                        columns={columns}
                        page={teachers}
                    >
                        {teachers.data.length ? (
                            teachers.data.map((teacher) => (
                                <tr key={teacher.id}>
                                    <td>
                                        <div className="teacherIndex__identity">
                                            {teacher.person.photo_url ? (
                                                <img
                                                    src={teacher.person.photo_url}
                                                    alt={`Foto de ${teacher.person.name}`}
                                                    className="teacherIndex__avatar"
                                                />
                                            ) : null}
                                            <strong>{teacher.person.name}</strong>
                                        </div>
                                    </td>
                                    <td>{teacher.person.email || "Não informado"}</td>
                                    <td>{teacher.registration || "—"}</td>
                                    <td>
                                        {can.view || can.delete ? (
                                            <TableActions>
                                                {can.view ? (
                                                    <Button
                                                        href={teacherShowUrl(teacher.id)}
                                                        size="sm"
                                                        tone="dark-blue"
                                                        iconOnly
                                                        leftIconClass="fa fa-eye"
                                                        aria-label={`Visualizar detalhes de ${teacher.person.name}`}
                                                    />
                                                ) : null}
                                                {can.delete ? (
                                                    <Button
                                                        tone="dark-red"
                                                        size="sm"
                                                        iconOnly
                                                        leftIconClass="fa fa-trash-o"
                                                        aria-label={`Excluir professor ${teacher.person.name}`}
                                                        disabled={busy}
                                                        onClick={() => {
                                                            cancelScheduled();
                                                            setTeacherToDelete(teacher);
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
                                <TableListEmpty colSpan={4}>
                                    Nenhum professor encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={teachers}
                    disabled={listLoading || busy}
                    paginationLabel="Paginação de professores"
                    onSelect={(page) => visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={teacherToDelete !== null}
                title="Excluir Professor"
                message={
                    <>
                        Deseja remover o professor <strong>{teacherToDelete?.person.name}</strong>?
                        {" "}Todos os dados vinculados serão excluídos.
                    </>
                }
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo professor..."
                confirmTone="dark-red"
                onClose={() => {
                    if (!busy) setTeacherToDelete(null);
                }}
                onConfirm={remove}
            />
        </TeacherPage>
    );
}
