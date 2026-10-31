import {
  useEffect,
  useRef,
  useState,
  type RefObject,
  type MutableRefObject,
} from "react";
import { loadExternalScript } from "@/utils/loadExternalScript";
import { createDefaultCalendarEvents } from "./EventCalendar.data";
import type {
  CalendarApi,
  CalendarCollection,
  CalendarDate,
  CalendarEvent,
  CalendarRuntime,
  EventCalendarProps,
  CalendarViewName,
} from "./EventCalendar.types";

interface CalendarEventElement {
  attr: (attributes: Record<string, string>) => void;
  on: (events: string, handler: (event: KeyboardEvent) => void) => void;
}

interface CalendarLimitCell {
  date: CalendarDate;
}

interface CalendarViewRuntime {
  name: CalendarViewName;
  start: CalendarDate;
  end: CalendarDate;
  calendar: {
    getDate: () => CalendarDate;
  };
}

function dateAriaLabel(date: string): string {
  const [year, month, day] = date.split("-");
  return `Abrir agendamentos de ${day}/${month}/${year}`;
}

function decorateCalendarDays(root: HTMLDivElement): void {
  root.querySelectorAll<HTMLElement>(".fc-day[data-date]").forEach((day) => {
    const date = day.dataset.date;
    if (!date) return;
    day.tabIndex = 0;
    day.setAttribute("role", "button");
    day.setAttribute("aria-label", dateAriaLabel(date));
  });
}
function addDays(date: string, days: number): string {
  const [year, month, day] = date.split("-").map(Number);
  const result = new Date(Date.UTC(year, month - 1, day + days));
  return result.toISOString().slice(0, 10);
}

/**
 * O plugin não grava a data nos cabeçalhos; nas visões de semana/dia cada
 * coluna corresponde a um dia a partir do início da visão.
 */
function decorateDayHeaders(
  root: HTMLDivElement,
  view: CalendarViewRuntime,
  selectedDate: string | undefined,
): void {
  if (view.name === "month") return;
  const start = view.start.format("YYYY-MM-DD");
  root
    .querySelectorAll<HTMLElement>(".fc-head .fc-day-header")
    .forEach((header, index) => {
      const date = addDays(start, index);
      const [year, month, day] = date.split("-");
      header.dataset.date = date;
      header.tabIndex = 0;
      header.setAttribute("role", "button");
      header.setAttribute("aria-label", `Selecionar o dia ${day}/${month}/${year}`);
      header.title = `Selecionar o dia ${day}/${month}`;
      header.classList.add("fc-day-header--selectable");
      header.classList.toggle("fc-day-header--selected", date === selectedDate);
    });
}

/**
 * O jQuery UI também registra `$.fn.datepicker` e, se terminar de carregar
 * depois do Bootstrap Datepicker (`Forms/DatePicker`), substitui o plugin
 * global usado pelos filtros e formulários. O calendário usa apenas o
 * draggable do jQuery UI (eventos externos com `droppable`), então ele só é
 * carregado nesse caso e o datepicker anterior é restaurado.
 */
async function loadJqueryUi(): Promise<void> {
  const fn = (window as Window & { jQuery?: { fn: Record<string, unknown> } })
    .jQuery?.fn;
  const datepicker = fn?.datepicker;
  await loadExternalScript("/assets/react/plugins/jquery-ui/jquery-ui.min.js");
  if (fn && datepicker) fn.datepicker = datepicker;
}

export function useEventCalendar(
  host: RefObject<HTMLDivElement | null>,
  props: EventCalendarProps,
): { ready: boolean; error: string | null } {
  const [ready, setReady] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const callbacks = useRef(props);
  callbacks.current = props;
  const calendar = useRef<CalendarCollection | null>(null);
  useEffect(() => {
    let cancelled = false;
    let resizeObserver: ResizeObserver | undefined;
    const root = host.current;
    async function initialize(): Promise<void> {
      await loadExternalScript("/assets/react/js/jquery.min.js");
      if (callbacks.current.droppable ?? true) await loadJqueryUi();
      for (const src of [
        "/assets/react/plugins/moment/moment.js",
        "/assets/react/plugins/fullcalendar/dist/fullcalendar.min.js",
      ])
        await loadExternalScript(src);
      const moment = (
        window as Window & { moment?: { locale: (key?: string) => string } }
      ).moment;
      const previousLocale = moment?.locale();
      await loadExternalScript(
        "/assets/react/plugins/fullcalendar/dist/lang/pt-br.js",
      );
      if (previousLocale) moment?.locale(previousLocale);
      if (cancelled || !root) return;
      const runtime = (window as Window & { jQuery?: CalendarRuntime }).jQuery;
      if (!runtime) throw new Error("jQuery indisponível.");
      const instance = runtime(root);
      const height = callbacks.current.height ?? window.innerHeight - 200;
      instance.fullCalendar({
        lang: "pt-br",
        slotDuration: "00:15:00",
        minTime: callbacks.current.minTime ?? "08:00:00",
        maxTime: callbacks.current.maxTime ?? "19:00:00",
        allDaySlot: callbacks.current.allDaySlot ?? true,
        slotEventOverlap: callbacks.current.slotEventOverlap ?? true,
        defaultView: callbacks.current.defaultView ?? "month",
        defaultDate: callbacks.current.initialDate,
        handleWindowResize: true,
        height,
        header: callbacks.current.header ?? {
          left: "prev,next today",
          center: "title",
          right: "month,agendaWeek,agendaDay",
        },
        events: callbacks.current.initialEvents ?? createDefaultCalendarEvents(),
        editable: callbacks.current.editable ?? true,
        droppable: callbacks.current.droppable ?? true,
        eventLimit: callbacks.current.eventLimit ?? true,
        selectable: callbacks.current.selectable ?? true,
        drop: function (this: HTMLElement, date: CalendarDate): void {
          instance.fullCalendar(
            "renderEvent",
            {
              title: this.dataset.title ?? "",
              start: date,
              className: `bg-${this.dataset.tone ?? "primary"}`,
            },
            true,
          );
          callbacks.current.onExternalDrop?.(this.dataset.categoryId ?? "");
        },
        select: (start: CalendarDate, end: CalendarDate): void => {
          callbacks.current.onSelect?.({ start, end });
          instance.fullCalendar("unselect");
        },
        dayClick: (date: CalendarDate): void => {
          callbacks.current.onDayClick?.(date.format("YYYY-MM-DD"));
        },
        eventLimitClick: (cell: CalendarLimitCell): false | string => {
          if (!callbacks.current.onMoreClick) return "popover";
          callbacks.current.onMoreClick(cell.date.format("YYYY-MM-DD"));
          return false;
        },
        eventRender: (
          event: CalendarEvent,
          element: CalendarEventElement,
        ): void => {
          element.attr({
            title: event.title,
            "aria-label": event.title,
            role: "button",
            tabindex: "0",
          });
          element.on("keydown.gnaiCalendar", (keyboardEvent) => {
            if (keyboardEvent.key !== "Enter" && keyboardEvent.key !== " ") return;
            keyboardEvent.preventDefault();
            callbacks.current.onEventClick?.(event);
          });
        },
        viewRender: (view: CalendarViewRuntime): void => {
          window.requestAnimationFrame(() => decorateCalendarDays(root));
          if (callbacks.current.onDayHeaderClick) {
            decorateDayHeaders(root, view, callbacks.current.selectedDate);
          }
          callbacks.current.onViewChange?.({
            name: view.name,
            date: view.calendar.getDate().format("YYYY-MM-DD"),
            start: view.start.format("YYYY-MM-DD"),
            end: view.end.format("YYYY-MM-DD"),
          });
        },
        eventClick: (
          event: CalendarEvent,
        ): void => callbacks.current.onEventClick?.(event),
      });
      calendar.current = instance;
      if (callbacks.current.api) {
        (callbacks.current.api as MutableRefObject<CalendarApi | null>).current = {
          create: (event) => instance.fullCalendar("renderEvent", event, true),
          update: (event, title) => {
            event.title = title;
            instance.fullCalendar("updateEvent", event);
          },
          remove: (event) =>
            instance.fullCalendar(
              "removeEvents",
              (candidate: { _id?: string }) => candidate._id === event._id,
            ),
          goToDate: (date) => instance.fullCalendar("gotoDate", date),
          replace: (events) => {
            instance.fullCalendar("removeEvents");
            events.forEach((event) =>
              instance.fullCalendar("renderEvent", event, true),
            );
          },
        };
      }
      resizeObserver = new ResizeObserver(() =>
        instance.fullCalendar("option", "height", height),
      );
      resizeObserver.observe(root);
      setReady(true);
    }
    void initialize().catch((cause: unknown) => {
      if (!cancelled) {
        setError("Não foi possível carregar o calendário.");
        console.error("Falha ao carregar o calendário.", cause);
      }
    });
    return () => {
      cancelled = true;
      resizeObserver?.disconnect();
      calendar.current?.fullCalendar("destroy");
      calendar.current = null;
      if (callbacks.current.api) {
        (callbacks.current.api as MutableRefObject<CalendarApi | null>).current = null;
      }
    };
  }, [host]);
  useEffect(() => {
    const externalHost = props.externalHost?.current;
    if (!ready || !externalHost) return;
    const runtime = (window as Window & { jQuery?: CalendarRuntime }).jQuery;
    if (!runtime) return;
    const elements = Array.from(
      externalHost.querySelectorAll<HTMLElement>(
        "[data-category-id]",
      ),
    );
    elements.forEach((element) =>
      runtime(element).draggable({
        zIndex: 999,
        revert: true,
        revertDuration: 0,
      }),
    );
    return () =>
      elements.forEach((element) => {
        if (
          runtime(element).data("ui-draggable") ||
          runtime(element).data("draggable")
        )
          runtime(element).draggable("destroy");
      });
  }, [ready, props.categories, props.externalHost]);

  useEffect(() => {
    if (!ready || !calendar.current || !props.initialEvents) return;
    calendar.current.fullCalendar("removeEvents");
    props.initialEvents.forEach((event) => {
      calendar.current?.fullCalendar("renderEvent", event, true);
    });
  }, [props.initialEvents, ready]);

  useEffect(() => {
    if (!ready || !calendar.current || !props.initialDate) return;
    calendar.current.fullCalendar("gotoDate", props.initialDate);
  }, [props.initialDate, ready]);

  useEffect(() => {
    const root = host.current;
    if (!ready || !root || !props.onDayClick) return undefined;

    const handleKeyDown = (event: KeyboardEvent): void => {
      if (event.key !== "Enter" && event.key !== " ") return;
      const target = event.target as HTMLElement;
      const day = target.closest<HTMLElement>(".fc-day[data-date]");
      if (!day?.dataset.date) return;
      event.preventDefault();
      callbacks.current.onDayClick?.(day.dataset.date);
    };

    root.addEventListener("keydown", handleKeyDown);
    decorateCalendarDays(root);
    return () => root.removeEventListener("keydown", handleKeyDown);
  }, [host, props.onDayClick, ready]);

  useEffect(() => {
    const root = host.current;
    if (!ready || !root || !props.onDayHeaderClick) return undefined;

    const headerDate = (target: EventTarget | null): string | undefined =>
      (target as HTMLElement | null)?.closest<HTMLElement>(
        ".fc-day-header--selectable[data-date]",
      )?.dataset.date;
    const handleClick = (event: MouseEvent): void => {
      const date = headerDate(event.target);
      if (date) callbacks.current.onDayHeaderClick?.(date);
    };
    const handleKeyDown = (event: KeyboardEvent): void => {
      if (event.key !== "Enter" && event.key !== " ") return;
      const date = headerDate(event.target);
      if (!date) return;
      event.preventDefault();
      callbacks.current.onDayHeaderClick?.(date);
    };

    root.addEventListener("click", handleClick);
    root.addEventListener("keydown", handleKeyDown);
    return () => {
      root.removeEventListener("click", handleClick);
      root.removeEventListener("keydown", handleKeyDown);
    };
  }, [host, props.onDayHeaderClick, ready]);

  return { ready, error };
}
