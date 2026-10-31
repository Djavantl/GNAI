import { usePage } from "@inertiajs/react";
import { useMemo, type ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import { dateLabel } from "@/components/Students/Students.utils";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    TableListCard,
    TableListEmpty,
    TableListFooter,
    TableListScroll,
    TableListTable,
    useTableListVisit,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import AeeRecordFilters, { aeeFilterValues } from "./AeeRecordFilters";
import type {
    AeeListFilterName,
    AeeListRecord,
    AeeRecordListPageProps,
} from "./AeeRecordList.types";
import { aeeRecordPdfUrl, aeeRecordShowUrl } from "./AeeRecord.utils";

interface AeeRecordListPageConfig {
    url: string;
    title: string;
    subtitle: string;
    breadcrumbLabel: string;
    idPrefix: string;
    fields: AeeListFilterName[];
    actions?: ReactNode;
}

const columns: TableListColumn[] = [
    { id: "date", label: "Data", width: "13%", minWidth: 90, fixed: "start" },
    { id: "course", label: "Curso", width: "22%", minWidth: 110 },
    { id: "students", label: "Estudantes", width: "30%", minWidth: 150 },
    { id: "professional", label: "Profissional responsável", width: "22%", minWidth: 120 },
    { id: "actions", label: "Ações", width: "13%", minWidth: 86, fixed: "end" },
];

/** Retorna o próprio `tr` para que o TableListTable possa ocultar colunas. */
function renderRecordRow(
    record: AeeListRecord,
    canView: boolean,
): React.JSX.Element {
    const session = record.attendance_session;
    const evaluations = record.student_evaluations ?? [];
    const date = dateLabel(session?.session_date);

    return (
        <tr key={record.id}>
            <td>
                <strong className="aeeRecordList__date">{date}</strong>
            </td>
            <td>
                <span className="aeeRecordList__stack">
                    {evaluations.length ? evaluations.map((evaluation) => {
                        const course = evaluation.student?.current_course?.course;

                        return (
                            <span
                                key={evaluation.id}
                                className={course ? undefined : "aeeRecordList__muted"}
                            >
                                {course?.name ?? "Sem curso"}
                            </span>
                        );
                    }) : (
                        <span className="aeeRecordList__muted">Sem curso</span>
                    )}
                </span>
            </td>
            <td>
                <span className="aeeRecordList__stack">
                    {evaluations.length ? evaluations.map((evaluation) => (
                        <span
                            key={evaluation.id}
                            className="aeeRecordList__student"
                        >
                            <span className="aeeRecordList__name">
                                {evaluation.student?.person?.name ?? "Estudante não informado"}
                            </span>
                            <TableLabel tone={evaluation.is_present ? "success" : "danger"}>
                                {evaluation.is_present ? "Presente" : "Ausente"}
                            </TableLabel>
                        </span>
                    )) : (
                        <span className="aeeRecordList__muted">Nenhum estudante avaliado</span>
                    )}
                </span>
            </td>
            <td>{session?.professional?.person?.name ?? "Não informado"}</td>
            <td>
                {canView ? (
                    <TableActions>
                        <Button
                            href={aeeRecordShowUrl(record.id)}
                            size="sm"
                            tone="dark-blue"
                            iconOnly
                            leftIconClass="fa fa-eye"
                            aria-label={`Visualizar atendimento de ${date}`}
                        />
                        <Button
                            href={aeeRecordPdfUrl(record.id)}
                            target="_blank"
                            rel="noopener noreferrer"
                            size="sm"
                            tone="cancel"
                            iconOnly
                            leftIconClass="fa fa-file-pdf-o"
                            aria-label={`Gerar PDF do atendimento de ${date}`}
                        />
                    </TableActions>
                ) : (
                    <span>Sem ações</span>
                )}
            </td>
        </tr>
    );
}

export default function AeeRecordListPage({
    aeeRecords,
    filters,
    students,
    professionals,
    courses,
    can,
    url,
    title,
    subtitle,
    breadcrumbLabel,
    idPrefix,
    fields,
    actions,
}: AeeRecordListPageProps & AeeRecordListPageConfig): React.JSX.Element {
    const errors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url,
        failureText: "Não foi possível atualizar os atendimentos especializados. Tente novamente.",
    });
    const initialValues = useMemo(
        () => aeeFilterValues(fields, filters),
        [fields, filters],
    );
    const emptyValues = useMemo(
        () => aeeFilterValues(fields),
        [fields],
    );
    const { values, update, clear, submit } = useAutoListFilters({
        initialValues,
        emptyValues,
        onSearch: (next) => list.visit(next),
    });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: breadcrumbLabel },
            ]}
            actions={actions}
        >
            <TableListCard
                aria-label="Listagem de atendimentos especializados"
                loading={list.loading}
                loadingLabel="Atualizando atendimentos especializados"
            >
                <AeeRecordFilters
                    idPrefix={idPrefix}
                    ariaLabel="Filtrar atendimentos especializados"
                    fields={fields}
                    values={values}
                    errors={errors}
                    students={students}
                    professionals={professionals}
                    courses={courses}
                    onChange={update}
                    onClear={clear}
                    onSubmit={submit}
                />
                <TableListScroll aria-label="Tabela de atendimentos especializados">
                    <TableListTable
                        className="aeeRecordList__table"
                        columns={columns}
                        page={aeeRecords}
                    >
                        {aeeRecords.data.length ? aeeRecords.data.map(
                            (record) => renderRecordRow(record, can.view),
                        ) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    Nenhum atendimento especializado encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={aeeRecords}
                    disabled={list.loading}
                    paginationLabel="Paginação de atendimentos especializados"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
        </BusinessPage>
    );
}
