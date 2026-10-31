import { useEffect, useRef } from "react";
import TableActions from "@/components/Tables/TableActions/TableActions";
import Button from "@/components/UI/Button/Button";
import Modal from "@/components/UI/Modal/Modal";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import { dateLabel } from "@/components/Students/Students.utils";
import SessionDeleteAction from "./SessionDeleteAction";
import { sessionShowUrl } from "./SessionForm.utils";
import type { SessionListRow } from "./SessionList.types";

export default function SessionDayModal({
    date,
    sessions,
    canView,
    onClose,
}: {
    date: string | null;
    sessions: SessionListRow[];
    canView: boolean;
    onClose: () => void;
}): React.JSX.Element {
    const body = useRef<HTMLDivElement | null>(null);
    const total = sessions.length;

    useEffect(() => {
        if (!date) return undefined;
        const returnFocus = document.activeElement as HTMLElement | null;
        const frame = window.requestAnimationFrame(() => {
            body.current
                ?.closest<HTMLElement>('[role="dialog"]')
                ?.querySelector<HTMLElement>(".modalLayer__close")
                ?.focus();
        });

        return () => {
            window.cancelAnimationFrame(frame);
            returnFocus?.focus();
        };
    }, [date]);

    return (
        <Modal
            open={date !== null}
            title={date ? `${dateLabel(date)} • ${total} ${total === 1 ? "agendamento" : "agendamentos"}` : undefined}
            size="large"
            effect="fadein"
            onClose={onClose}
            contentClassName="sessionsPage__dayModal"
            bodyClassName="sessionsPage__dayModalBody"
            footer={(
                <Button
                    tone="cancel"
                    label="Fechar"
                    title="Fechar agendamentos do dia"
                    onClick={onClose}
                />
            )}
        >
            <div ref={body}>
                {sessions.length ? (
                    <SessionDeleteAction>
                        {({ open, processing }) => (
                            <ol className="sessionsPage__dayModalSessions">
                                {sessions.map((session) => (
                                    <li key={session.id} className="sessionsPage__dayModalSession">
                                        <header className="sessionsPage__dayModalSessionHeader">
                                            <div>
                                                <span className="sessionsPage__dayModalTime">
                                                    {session.start_time ?? "--:--"} a {session.end_time ?? "--:--"}
                                                </span>
                                                <span className="sessionsPage__dayModalType">
                                                    {session.type_label}
                                                </span>
                                            </div>
                                            <TableLabel tone={session.status_color}>
                                                {session.status_label}
                                            </TableLabel>
                                        </header>
                                        <dl className="sessionsPage__dayModalDetails">
                                            <div>
                                                <dt>Aluno(s)</dt>
                                                <dd>{session.student_names.join(", ") || "Sem alunos"}</dd>
                                            </div>
                                            <div>
                                                <dt>Professor</dt>
                                                <dd>{session.professional_name}</dd>
                                            </div>
                                            <div>
                                                <dt>Registro</dt>
                                                <dd className={session.has_record ? "is-complete" : "is-pending"}>
                                                    <i
                                                        className={session.has_record ? "fa fa-check-circle" : "fa fa-clock-o"}
                                                        aria-hidden="true"
                                                    />
                                                    {session.record_label}
                                                </dd>
                                            </div>
                                        </dl>
                                        {canView || session.can_delete ? (
                                            <TableActions>
                                                {canView ? (
                                                    <Button
                                                        href={sessionShowUrl(session.id)}
                                                        size="sm"
                                                        tone="dark-blue"
                                                        leftIconClass="fa fa-eye"
                                                        label="Visualizar"
                                                        title={`Visualizar agendamento das ${session.start_time ?? ""}`}
                                                    />
                                                ) : null}
                                                {session.can_delete ? (
                                                    <Button
                                                        type="button"
                                                        size="sm"
                                                        tone="dark-red"
                                                        leftIconClass="fa fa-trash-o"
                                                        label="Excluir"
                                                        title={`Excluir agendamento das ${session.start_time ?? ""}`}
                                                        disabled={processing}
                                                        onClick={() => open(session)}
                                                    />
                                                ) : null}
                                            </TableActions>
                                        ) : null}
                                    </li>
                                ))}
                            </ol>
                        )}
                    </SessionDeleteAction>
                ) : (
                    <div className="sessionsPage__dayModalEmpty">
                        <i className="fa fa-calendar-o" aria-hidden="true" />
                        <h5>Nenhum agendamento nesta data</h5>
                        <p>Selecione outro dia ou ajuste os filtros para consultar a agenda.</p>
                    </div>
                )}
            </div>
        </Modal>
    );
}
