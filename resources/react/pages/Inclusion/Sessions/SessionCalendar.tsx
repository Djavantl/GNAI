import { useEffect, useMemo, useState } from "react";
import { router } from "@inertiajs/react";
import EventCalendar from "@/components/UI/EventCalendar/EventCalendar";
import type {
    CalendarEvent,
    CalendarViewName,
} from "@/components/UI/EventCalendar/EventCalendar.types";
import SessionDayModal from "./SessionDayModal";
import { sessionShowUrl } from "./SessionForm.utils";
import type { SessionCalendarData } from "./SessionList.types";

function eventTitle(session: SessionCalendarData["sessions"][number]): string {
    return session.student_names.join(", ") || "Sem alunos";
}

const defaultFirstHour = 8;
const defaultLastHour = 19;

function hourOf(time: string): number {
    const [hours, minutes] = time.split(":").map(Number);
    return hours + minutes / 60;
}

function clockTime(hour: number): string {
    return `${String(hour).padStart(2, "0")}:00:00`;
}

/** Amplia a grade padrão (08h a 19h) para não ocultar atendimentos fora dela. */
function visibleHours(sessions: SessionCalendarData["sessions"]): { minTime: string; maxTime: string } {
    let first = defaultFirstHour;
    let last = defaultLastHour;
    sessions.forEach((session) => {
        if (!session.start_time) return;
        const start = hourOf(session.start_time);
        const end = session.end_time ? hourOf(session.end_time) : start + 1;
        first = Math.min(first, Math.floor(start));
        last = Math.max(last, Math.ceil(end));
    });
    return { minTime: clockTime(first), maxTime: clockTime(Math.min(last, 24)) };
}

function eventDate(event: CalendarEvent): string | null {
    if (event.sessionDate) return event.sessionDate;
    if (typeof event.start === "string") return event.start.slice(0, 10);
    if (event.start instanceof Date) {
        const year = event.start.getFullYear();
        const month = String(event.start.getMonth() + 1).padStart(2, "0");
        const day = String(event.start.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    }
    return event.start.format("YYYY-MM-DD");
}

export default function SessionCalendar({
    calendar,
    referenceDate,
    canView,
    onReferenceDate,
}: {
    calendar: SessionCalendarData;
    referenceDate: string;
    canView: boolean;
    onReferenceDate: (date: string) => void;
}): React.JSX.Element {
    const [selectedDate, setSelectedDate] = useState(referenceDate);
    const [modalDate, setModalDate] = useState<string | null>(null);
    const [view, setView] = useState<CalendarViewName>("agendaWeek");
    const hours = useMemo(() => visibleHours(calendar.sessions), [calendar.sessions]);
    const sessionsByDate = useMemo(() => {
        const grouped = new Map<string, SessionCalendarData["sessions"]>();
        calendar.sessions.forEach((session) => {
            if (!session.session_date) return;
            const sessions = grouped.get(session.session_date) ?? [];
            sessions.push(session);
            grouped.set(session.session_date, sessions);
        });
        return grouped;
    }, [calendar.sessions]);
    const events = useMemo<CalendarEvent[]>(
        () => calendar.sessions.flatMap((session) => {
            if (!session.session_date) return [];
            return [{
                id: String(session.id),
                sessionDate: session.session_date,
                title: eventTitle(session),
                start: session.start_time
                    ? `${session.session_date}T${session.start_time}`
                    : session.session_date,
                end: session.end_time
                    ? `${session.session_date}T${session.end_time}`
                    : undefined,
                allDay: !session.start_time,
                className: `bg-${session.status_color}`,
            }];
        }),
        [calendar.sessions],
    );
    useEffect(() => {
        setSelectedDate(referenceDate);
    }, [referenceDate]);

    function selectDate(date: string): void {
        setSelectedDate(date);
        setModalDate(date);
    }

    return (
        <>
            <div className="sessionsPage__calendarViewport">
                <EventCalendar
                    // O plugin só aplica a faixa de horários na inicialização.
                    key={`${hours.minTime}-${hours.maxTime}`}
                    className="eventCalendar--sessions"
                    ariaLabel="Calendário de agendamentos"
                    initialDate={referenceDate}
                    defaultView={view}
                    minTime={hours.minTime}
                    maxTime={hours.maxTime}
                    allDaySlot={false}
                    slotEventOverlap={false}
                    selectedDate={selectedDate}
                    initialEvents={events}
                    editable={false}
                    droppable={false}
                    selectable={false}
                    onDayClick={selectDate}
                    onMoreClick={selectDate}
                    onDayHeaderClick={(date) => {
                        setSelectedDate(date);
                        if (date !== referenceDate) onReferenceDate(date);
                    }}
                    onViewChange={(change) => {
                        setView(change.name);
                        if (change.date !== referenceDate) onReferenceDate(change.date);
                    }}
                    onEventClick={(event) => {
                        if (canView && event.id) {
                            router.visit(sessionShowUrl(Number(event.id)));
                            return;
                        }
                        const date = eventDate(event);
                        if (date) selectDate(date);
                    }}
                />
            </div>
            <SessionDayModal
                date={modalDate}
                sessions={modalDate ? sessionsByDate.get(modalDate) ?? [] : []}
                canView={canView}
                onClose={() => setModalDate(null)}
            />
        </>
    );
}
