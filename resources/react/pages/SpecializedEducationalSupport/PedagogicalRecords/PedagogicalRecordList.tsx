import { router, usePage } from "@inertiajs/react";
import { useMemo, useRef, useState, type CSSProperties } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import { aeeBase, dateLabel } from "@/components/Students/Students.utils";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    TableListCard,
    TableListClearButton,
    TableListEmpty,
    TableListFilterActions,
    TableListFooter,
    TableListMoreFilters,
    TableListScroll,
    TableListTable,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters, { compactListFilters } from "@/hooks/useAutoListFilters";
import type {
    PedagogicalRecordFilterName,
    PedagogicalRecordFilters,
    PedagogicalRecordListProps,
    PedagogicalRecordListRow,
} from "./PedagogicalRecordList.types";
import "./PedagogicalRecordList.css";

type Props = PedagogicalRecordListProps & {
    url: string;
    variant: "general" | "student";
    primaryFilters: PedagogicalRecordFilterName[];
    moreFilters?: PedagogicalRecordFilterName[];
    pageParam?: string;
    studentId?: number;
};

const recordsBase = `${aeeBase}/pedagogical-records`;
const emptyFilters: PedagogicalRecordFilters = {
    student: "",
    professional_id: "",
    course_id: "",
    is_present: "",
    with_guardians: "",
};

const generalColumns: TableListColumn[] = [
    { id: "date", label: "Data", width: "13%", minWidth: 90, fixed: "start" },
    { id: "course", label: "Curso", width: "19%", minWidth: 100 },
    { id: "student", label: "Estudante", width: "26%", minWidth: 140 },
    { id: "professional", label: "Profissional responsável", width: "20%", minWidth: 120 },
    { id: "guardians", label: "Com responsáveis", width: "11%", minWidth: 90 },
    { id: "actions", label: "Ações", width: "11%", minWidth: 96, fixed: "end" },
];

const studentColumns: TableListColumn[] = [
    { id: "date", label: "Data", width: "13%", minWidth: 86, fixed: "start" },
    { id: "time", label: "Horário", width: "13%", minWidth: 84 },
    { id: "professional", label: "Profissional", width: "19%", minWidth: 110 },
    { id: "duration", label: "Duração", width: "11%", minWidth: 72 },
    { id: "presence", label: "Presença", width: "12%", minWidth: 80 },
    { id: "guardians", label: "Com responsáveis", width: "12%", minWidth: 90 },
    { id: "actions", label: "Ações", width: "20%", minWidth: 176, fixed: "end" },
];

function sessionTime(record: PedagogicalRecordListRow): string {
    if (!record.start_time) return "—";
    const start = record.start_time.slice(0, 5);
    const end = record.end_time?.slice(0, 5);

    return end ? `${start} – ${end}` : start;
}

function presenceLabel(record: PedagogicalRecordListRow): React.JSX.Element {
    return (
        <TableLabel tone={record.is_present ? "success" : "danger"}>
            {record.is_present ? "Presente" : "Ausente"}
        </TableLabel>
    );
}

function guardiansLabel(record: PedagogicalRecordListRow): React.JSX.Element {
    return (
        <TableLabel tone={record.has_guardians ? "success" : "inverse"}>
            {record.has_guardians ? "Sim" : "Não"}
        </TableLabel>
    );
}

export default function PedagogicalRecordList({
    pedagogicalRecords,
    students,
    professionals,
    courses,
    filters: initialFilters,
    can,
    url,
    variant,
    primaryFilters,
    moreFilters = [],
    pageParam = "page",
    studentId,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const pending = useRef(false);
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [recordToDelete, setRecordToDelete] = useState<PedagogicalRecordListRow | null>(null);
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters }),
        [initialFilters],
    );
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (next) => visit(next),
    });
    const isStudent = variant === "student";
    const columns = isStudent ? studentColumns : generalColumns;
    const studentQuery = isStudent && studentId ? `?student_id=${studentId}` : "";
    const filterFields: Record<PedagogicalRecordFilterName, {
        label: string;
        placeholder: string;
        options: Array<{ value: string; label: string }>;
    }> = {
        student: {
            label: "Estudante",
            placeholder: "Estudante (Todos)",
            options: students.map((item) => ({ value: String(item.id), label: item.name })),
        },
        professional_id: {
            label: "Profissional",
            placeholder: "Profissional (Todos)",
            options: professionals.map((item) => ({ value: String(item.id), label: item.name })),
        },
        course_id: {
            label: "Curso",
            placeholder: "Curso (Todos)",
            options: [
                { value: "0", label: "Sem curso" },
                ...courses.map((item) => ({ value: String(item.id), label: item.name })),
            ],
        },
        is_present: {
            label: "Presença",
            placeholder: "Presença (Todas)",
            options: [
                { value: "1", label: "Presente" },
                { value: "0", label: "Ausente" },
            ],
        },
        with_guardians: {
            label: "Com responsáveis",
            placeholder: "Com responsáveis (Todos)",
            options: [
                { value: "1", label: "Sim" },
                { value: "0", label: "Não" },
            ],
        },
    };

    function failure(text: string): void {
        notify({ tone: "error", title: "Não foi possível concluir", text });
    }

    function visit(next: PedagogicalRecordFilters, page = 1): void {
        if (pending.current) return;
        pending.current = true;
        setBusy(true);
        setListLoading(true);
        router.get(url, { ...compactListFilters(next), [pageParam]: page }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onNetworkError: () => {
                failure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                failure("Não foi possível atualizar os atendimentos pedagógicos. Tente novamente.");
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
        if (pending.current || !recordToDelete) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        router.delete(`${recordsBase}/${recordToDelete.id}${studentQuery}`, {
            preserveScroll: true,
            onNetworkError: () => {
                failure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                failure("Não foi possível excluir o atendimento pedagógico. Tente novamente.");
                return false;
            },
            onSuccess: () => setRecordToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    function renderFilter(name: PedagogicalRecordFilterName): React.JSX.Element {
        const field = filterFields[name];
        const id = `pedagogical-record-filter-${name.replace("_", "-")}`;
        const error = pageErrors[name];

        return (
            <FormField key={name} id={id} label={field.label} error={error}>
                <SelectField
                    id={id}
                    name={name}
                    label={field.label}
                    placeholder={field.placeholder}
                    value={values[name]}
                    options={field.options}
                    disabled={busy}
                    aria-invalid={!!error}
                    aria-describedby={error ? `${id}-error` : undefined}
                    onChange={(event) => update(name, event.target.value)}
                />
            </FormField>
        );
    }

    function renderActions(record: PedagogicalRecordListRow): React.JSX.Element {
        const subject = isStudent
            ? `atendimento de ${dateLabel(record.session_date)}`
            : `atendimento de ${record.student_name ?? "estudante não informado"} em ${dateLabel(record.session_date)}`;
        const canUpdate = isStudent && can.update && record.can_manage;
        const canDelete = isStudent && can.delete && record.can_manage;

        if (!can.view && !canUpdate && !canDelete) {
            return <span>—</span>;
        }

        return (
            <TableActions>
                {can.view ? (
                    <Button
                        href={`${recordsBase}/${record.id}/show${studentQuery}`}
                        size="sm"
                        tone="dark-blue"
                        iconOnly
                        leftIconClass="fa fa-eye"
                        aria-label={`Visualizar ${subject}`}
                    />
                ) : null}
                {can.view ? (
                    <Button
                        href={`${recordsBase}/${record.id}/pdf`}
                        target="_blank"
                        rel="noopener noreferrer"
                        size="sm"
                        tone="cancel"
                        iconOnly
                        leftIconClass="fa fa-file-pdf-o"
                        aria-label={`Gerar PDF do ${subject}`}
                    />
                ) : null}
                {canUpdate ? (
                    <Button
                        href={`${recordsBase}/${record.id}/edit${studentQuery}`}
                        size="sm"
                        tone="warning"
                        iconOnly
                        leftIconClass="fa fa-pencil"
                        aria-label={`Editar ${subject}`}
                    />
                ) : null}
                {canDelete ? (
                    <Button
                        type="button"
                        tone="dark-red"
                        size="sm"
                        iconOnly
                        leftIconClass="fa fa-trash-o"
                        aria-label={`Excluir ${subject}`}
                        disabled={busy}
                        onClick={() => {
                            cancelScheduled();
                            setRecordToDelete(record);
                        }}
                    />
                ) : null}
            </TableActions>
        );
    }

    function renderRow(record: PedagogicalRecordListRow): React.JSX.Element {
        const date = <strong>{dateLabel(record.session_date)}</strong>;

        if (isStudent) {
            return (
                <tr key={record.id}>
                    <td>{date}</td>
                    <td>{sessionTime(record)}</td>
                    <td>{record.professional_name ?? "Não informado"}</td>
                    <td>{record.duration ?? "—"}</td>
                    <td>{presenceLabel(record)}</td>
                    <td>{guardiansLabel(record)}</td>
                    <td>{renderActions(record)}</td>
                </tr>
            );
        }

        return (
            <tr key={record.id}>
                <td>{date}</td>
                <td>
                    {record.course_name ?? (
                        <span className="pedagogicalRecordList__muted">Sem curso</span>
                    )}
                </td>
                <td>
                    <span className="pedagogicalRecordList__student">
                        <span className="pedagogicalRecordList__studentName">
                            {record.student_name ?? "Estudante não informado"}
                        </span>
                        {presenceLabel(record)}
                    </span>
                </td>
                <td>{record.professional_name ?? "Não informado"}</td>
                <td>{guardiansLabel(record)}</td>
                <td>{renderActions(record)}</td>
            </tr>
        );
    }

    return (
        <>
            <TableListCard
                aria-label="Listagem de atendimentos pedagógicos"
                aria-busy={busy}
                loading={listLoading}
                loadingLabel="Atualizando atendimentos pedagógicos"
            >
                <FormLayout
                    aria-label="Filtrar atendimentos pedagógicos"
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div
                        className="pedagogicalRecordList__filters"
                        style={{ "--pedagogical-record-filter-count": primaryFilters.length } as CSSProperties}
                    >
                        {primaryFilters.map(renderFilter)}
                        <TableListFilterActions>
                            <TableListClearButton disabled={busy} onClick={clear} />
                        </TableListFilterActions>
                    </div>
                    {moreFilters.length ? (
                        <TableListMoreFilters active={moreFilters.some((name) => Boolean(initialValues[name]))}>
                            <div className="pedagogicalRecordList__moreFilters">
                                {moreFilters.map(renderFilter)}
                            </div>
                        </TableListMoreFilters>
                    ) : null}
                </FormLayout>
                <TableListScroll aria-label="Tabela de atendimentos pedagógicos">
                    <TableListTable
                        className="pedagogicalRecordList__table"
                        columns={columns}
                        page={pedagogicalRecords}
                    >
                        {pedagogicalRecords.data.length ? pedagogicalRecords.data.map(renderRow) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    Nenhum atendimento pedagógico encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={pedagogicalRecords}
                    disabled={busy}
                    paginationLabel="Paginação de atendimentos pedagógicos"
                    onSelect={(page) => visit(values, page)}
                />
            </TableListCard>
            {isStudent ? (
                <ConfirmActionModal
                    open={recordToDelete !== null}
                    title="Excluir Atendimento Pedagógico"
                    message="Deseja excluir este atendimento pedagógico?"
                    details={recordToDelete ? (
                        <>
                            Atendimento de <strong>{dateLabel(recordToDelete.session_date)}</strong>
                            {recordToDelete.professional_name ? ` com ${recordToDelete.professional_name}` : ""}.
                        </>
                    ) : undefined}
                    processing={busy}
                    confirmLabel="Confirmar exclusão"
                    processingLabel="Excluindo atendimento..."
                    confirmTone="dark-red"
                    onClose={() => {
                        if (!busy) setRecordToDelete(null);
                    }}
                    onConfirm={remove}
                />
            ) : null}
        </>
    );
}
