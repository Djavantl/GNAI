import { usePage } from "@inertiajs/react";
import { useMemo } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import DeleteStudentRecord from "@/components/Students/DeleteStudentRecord";
import StudentRecordReturnButton from "@/components/Students/StudentRecordReturnButton";
import {
    dateLabel,
    studentRecordUrl,
    studentsBase,
} from "@/components/Students/Students.utils";
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
import AeeRecordFilters, {
    aeeFilterValues,
} from "@/pages/Inclusion/AeeRecords/AeeRecordFilters";
import type { AeeStudent } from "@/pages/Inclusion/AeeRecords/AeeRecord.types";
import type {
    AeeFilterOptions,
    AeeListFilterName,
    AeeListFilters,
    AeePaginator,
    AeeStudentEvaluationListItem,
} from "@/pages/Inclusion/AeeRecords/AeeRecordList.types";
import {
    studentAeeEvaluationUrl,
    studentAeeRecordsUrl,
} from "@/pages/Inclusion/AeeRecords/AeeRecord.utils";

type Props = AeeFilterOptions & {
    student: AeeStudent;
    aeeEvaluations: AeePaginator<AeeStudentEvaluationListItem>;
    filters: Partial<AeeListFilters>;
    can: {
        view: boolean;
        update: boolean;
        delete: boolean;
        view_all: boolean;
    };
};

const recordTab = "aee-attendance";
const columns: TableListColumn[] = [
    { id: "date", label: "Data", width: "15%", minWidth: 90, fixed: "start" },
    { id: "professional", label: "Profissional", width: "31%", minWidth: 120 },
    { id: "duration", label: "Duração", width: "16%", minWidth: 76 },
    { id: "presence", label: "Presença", width: "16%", minWidth: 82 },
    { id: "actions", label: "Ações", width: "22%", minWidth: 120, fixed: "end" },
];

export default function InclusionStudentsAeeRecordsIndexPage({
    student,
    aeeEvaluations,
    filters,
    professionals,
    can,
}: Props): React.JSX.Element {
    const errors = usePage().props.errors as Record<string, string | undefined>;
    const studentName = student.person?.name ?? `Aluno #${student.id}`;
    const fields = useMemo<AeeListFilterName[]>(
        () => (can.view_all ? ["professional_id", "is_present"] : ["is_present"]),
        [can.view_all],
    );
    const list = useTableListVisit({
        url: studentAeeRecordsUrl(student.id),
        failureText: "Não foi possível atualizar os registros de atendimento AEE. Tente novamente.",
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
            title="Histórico de Registros de Atendimento AEE"
            subtitle={`Aluno: ${studentName}`}
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Alunos", href: studentsBase },
                { label: studentName, href: studentRecordUrl(student.id, recordTab) },
                { label: "Registros de atendimentos AEE" },
            ]}
            actions={(
                <StudentRecordReturnButton
                    studentId={student.id}
                    tab={recordTab}
                />
            )}
        >
            <TableListCard
                aria-label="Listagem de registros de atendimento AEE do aluno"
                loading={list.loading}
                loadingLabel="Atualizando registros de atendimento AEE"
            >
                <AeeRecordFilters
                    idPrefix="student-aee-records"
                    ariaLabel="Filtrar registros de atendimento AEE do aluno"
                    fields={fields}
                    values={values}
                    errors={errors}
                    professionals={professionals}
                    onChange={update}
                    onClear={clear}
                    onSubmit={submit}
                />
                <TableListScroll aria-label="Tabela de registros de atendimento AEE do aluno">
                    <TableListTable
                        columns={columns}
                        page={aeeEvaluations}
                    >
                        {aeeEvaluations.data.length ? aeeEvaluations.data.map((evaluation) => {
                            const session = evaluation.aee_record?.attendance_session;
                            const date = dateLabel(session?.session_date);
                            const canUpdate = can.update && evaluation.can_manage;
                            const canDelete = can.delete && evaluation.can_manage;

                            return (
                                <tr key={evaluation.id}>
                                    <td>
                                        <strong className="aeeRecordList__date">{date}</strong>
                                    </td>
                                    <td>{session?.professional?.person?.name ?? "Não informado"}</td>
                                    <td>{evaluation.aee_record?.duration || "Não informado"}</td>
                                    <td>
                                        <TableLabel tone={evaluation.is_present ? "success" : "danger"}>
                                            <i
                                                className={evaluation.is_present
                                                    ? "fa fa-check-circle"
                                                    : "fa fa-times-circle"}
                                                aria-hidden="true"
                                            />
                                            {" "}
                                            {evaluation.is_present ? "Presente" : "Ausente"}
                                        </TableLabel>
                                    </td>
                                    <td>
                                        {can.view || canUpdate || canDelete ? (
                                            <TableActions>
                                                {can.view ? (
                                                    <Button
                                                        href={studentAeeEvaluationUrl(student.id, evaluation.id)}
                                                        size="sm"
                                                        tone="dark-blue"
                                                        iconOnly
                                                        leftIconClass="fa fa-eye"
                                                        aria-label={`Visualizar atendimento de ${date}`}
                                                    />
                                                ) : null}
                                                {canUpdate ? (
                                                    <Button
                                                        href={studentAeeEvaluationUrl(student.id, evaluation.id, "edit")}
                                                        size="sm"
                                                        tone="warning"
                                                        iconOnly
                                                        leftIconClass="fa fa-pencil"
                                                        aria-label={`Editar atendimento de ${date}`}
                                                    />
                                                ) : null}
                                                {canDelete ? (
                                                    <DeleteStudentRecord
                                                        action={studentAeeEvaluationUrl(student.id, evaluation.id, "")}
                                                        title="Excluir registro do aluno"
                                                        message="Excluir apenas o registro deste aluno neste atendimento AEE?"
                                                        ariaLabel={`Excluir atendimento de ${date}`}
                                                    />
                                                ) : null}
                                            </TableActions>
                                        ) : (
                                            <span>Sem ações</span>
                                        )}
                                    </td>
                                </tr>
                            );
                        }) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    Nenhum registro encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={aeeEvaluations}
                    disabled={list.loading}
                    paginationLabel="Paginação de registros de atendimento AEE do aluno"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
        </BusinessPage>
    );
}
