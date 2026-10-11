import { useRef } from "react";
import { useEventCalendar } from "./useEventCalendar";
import type { EventCalendarProps } from "./EventCalendar.types";
import "./EventCalendar.plugin.css";
import "./EventCalendar.css";
export default function EventCalendar(props: EventCalendarProps): React.JSX.Element {
  const host = useRef<HTMLDivElement>(null);
  const { ready, error } = useEventCalendar(host, props);
  return (
    <>
      <div
        ref={host}
        className="eventCalendar"
        aria-label="Calendário de eventos"
        aria-busy={!ready && !error}
      />
      {error && <p role="alert">{error}</p>}
    </>
  );
}
