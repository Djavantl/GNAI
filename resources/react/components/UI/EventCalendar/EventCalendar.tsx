import { useEffect, useRef } from "react";
import { useEventCalendar } from "./useEventCalendar";
import type { EventCalendarProps } from "./EventCalendar.types";
import "./EventCalendar.plugin.css";
import "./EventCalendar.css";
export default function EventCalendar(props: EventCalendarProps): React.JSX.Element {
  const host = useRef<HTMLDivElement>(null);
  const { ready, error } = useEventCalendar(host, props);

  useEffect(() => {
    const root = host.current;
    if (!root) return;

    root
      .querySelectorAll(".fc-day--selected")
      .forEach((element) => element.classList.remove("fc-day--selected"));
    root
      .querySelectorAll<HTMLElement>(".fc-day-header[data-date]")
      .forEach((header) =>
        header.classList.toggle(
          "fc-day-header--selected",
          header.dataset.date === props.selectedDate,
        ),
      );

    if (props.selectedDate) {
      root
        .querySelector(`.fc-day[data-date="${props.selectedDate}"]`)
        ?.classList.add("fc-day--selected");
    }
  }, [props.initialDate, props.selectedDate, ready]);

  return (
    <>
      <div
        ref={host}
        className={`eventCalendar ${props.className ?? ""}`}
        aria-label={props.ariaLabel ?? "Calendário de eventos"}
        aria-busy={!ready && !error}
      />
      {error ? <p className="eventCalendar__error" role="alert">{error}</p> : null}
    </>
  );
}
