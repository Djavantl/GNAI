import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    TableListEmpty,
    TableListFooter,
    TableListScroll,
    TableListTable,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import { dateLabel } from "@/components/Students/Students.utils";
import SessionDeleteAction from "./SessionDeleteAction";
import { sessionShowUrl } from "./SessionForm.utils";
import type { SessionListPaginator, SessionListRow } from "./SessionList.types";

function buildColumns(
    showStudents: boolean,
    showProfessional: boolean,
): TableListColumn[] {
    return [
        { id: "date", label: "Data", width: "16%", minWidth: 100, fixed: "start" },
        ...(showStudents
            ? [{ id: "students", label: "Aluno", width: showProfessional ? "24%" : "36%", minWidth: 120 }]
            : []),
        ...(showProfessional
            ? [{ id: "professional", label: "Profissional", width: showStudents ? "22%" : "36%", minWidth: 120 }]
            : []),
        { id: "type", label: "Tipo", width: "12%", minWidth: 80 },
        { id: "status", label: "Status", width: "12%", minWidth: 90 },
        { id: "actions", label: "Ações", width: "14%", minWidth: 86, fixed: "end" },
    ];
}

function sessionDescription(session: SessionListRow): string {
    return `agendamento de ${dateLabel(session.session_date)}`;
}

export default function SessionTable({
    sessions,
    canView,
    showStudents = false,
    showProfessional = false,
    showTime = false,
    loading = false,
    emptyMessage = "Nenhum agendamento encontrado.",
    onPage,
}: {
    sessions: SessionListPaginator;
    canView: boolean;
    showStudents?: boolean;
    showProfessional?: boolean;
    showTime?: boolean;
    loading?: boolean;
    emptyMessage?: string;
    onPage: (page: number) => void;
}): React.JSX.Element {
    const columns = buildColumns(showStudents, showProfessional);

    return (
        <SessionDeleteAction>
            {({ open, processing }) => (
                <>
                    <TableListScroll aria-label="Tabela de agendamentos">
                        <TableListTable
                            className="sessionsPage__table"
                            columns={columns}
                            page={sessions}
                        >
                            {sessions.data.length ? sessions.data.map((session) => (
                                <tr key={session.id}>
                                    <td>
                                        <span className="sessionsPage__date">
                                            <strong>{dateLabel(session.session_date)}</strong>
                                            {showTime && session.start_time ? (
                                                <small>{session.start_time}</small>
                                            ) : null}
                                        </span>
                                    </td>
                                    {showStudents ? (
                                        <td>
                                            {session.student_names.length ? (
                                                session.student_names.join(", ")
                                            ) : (
                                                <span className="sessionsPage__muted">Sem alunos</span>
                                            )}
                                        </td>
                                    ) : null}
                                    {showProfessional ? <td>{session.professional_name}</td> : null}
                                    <td>{session.type_label}</td>
                                    <td>
                                        <TableLabel tone={session.status_color}>
                                            {session.status_label}
                                        </TableLabel>
                                    </td>
                                    <td>
                                        {canView || session.can_delete ? (
                                            <TableActions>
                                                {canView ? (
                                                    <Button
                                                        href={sessionShowUrl(session.id)}
                                                        size="sm"
                                                        tone="dark-blue"
                                                        iconOnly
                                                        leftIconClass="fa fa-eye"
                                                        aria-label={`Visualizar ${sessionDescription(session)}`}
                                                    />
                                                ) : null}
                                                {session.can_delete ? (
                                                    <Button
                                                        tone="dark-red"
                                                        size="sm"
                                                        iconOnly
                                                        leftIconClass="fa fa-trash-o"
                                                        aria-label={`Excluir ${sessionDescription(session)}`}
                                                        disabled={processing}
                                                        onClick={() => open(session)}
                                                    />
                                                ) : null}
                                            </TableActions>
                                        ) : (
                                            <span>Sem ações</span>
                                        )}
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <TableListEmpty colSpan={columns.length}>
                                        {emptyMessage}
                                    </TableListEmpty>
                                </tr>
                            )}
                        </TableListTable>
                    </TableListScroll>
                    <TableListFooter
                        page={sessions}
                        disabled={loading}
                        paginationLabel="Paginação de agendamentos"
                        onSelect={onPage}
                    />
                </>
            )}
        </SessionDeleteAction>
    );
}
