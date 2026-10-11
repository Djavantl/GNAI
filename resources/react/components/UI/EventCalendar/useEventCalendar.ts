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
  CalendarRuntime,
  EventCalendarProps,
} from "./EventCalendar.types";
export function useEventCalendar(
  host: RefObject<HTMLDivElement | null>,
  props: EventCalendarProps,
): { ready: boolean; error: string | null } {
  const [ready, setReady] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const callbacks = useRef(props);
  callbacks.current = props;
  const calendar = useRef<CalendarCollection | null>(null);
  const initialEvents = useRef(props.initialEvents);
  useEffect(() => {
    let cancelled = false;
    let resizeObserver: ResizeObserver | undefined;
    const root = host.current;
    async function initialize(): Promise<void> {
      for (const src of [
        "/assets/react/js/jquery.min.js",
        "/assets/react/plugins/jquery-ui/jquery-ui.min.js",
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
      const height = window.innerHeight - 200;
      instance.fullCalendar({
        lang: "pt-br",
        slotDuration: "00:15:00",
        minTime: "08:00:00",
        maxTime: "19:00:00",
        defaultView: "month",
        handleWindowResize: true,
        height,
        header: {
          left: "prev,next today",
          center: "title",
          right: "month,agendaWeek,agendaDay",
        },
        events: initialEvents.current ?? createDefaultCalendarEvents(),
        editable: true,
        droppable: true,
        eventLimit: true,
        selectable: true,
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
          callbacks.current.onExternalDrop(this.dataset.categoryId ?? "");
        },
        select: (start: CalendarDate, end: CalendarDate): void => {
          callbacks.current.onSelect({ start, end });
          instance.fullCalendar("unselect");
        },
        eventClick: (
          event: Parameters<EventCalendarProps["onEventClick"]>[0],
        ): void => callbacks.current.onEventClick(event),
      });
      calendar.current = instance;
      (callbacks.current.api as MutableRefObject<CalendarApi | null>).current =
        {
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
        };
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
      (callbacks.current.api as MutableRefObject<CalendarApi | null>).current =
        null;
    };
  }, [host]);
  useEffect(() => {
    if (!ready || !props.externalHost.current) return;
    const runtime = (window as Window & { jQuery?: CalendarRuntime }).jQuery;
    if (!runtime) return;
    const elements = Array.from(
      props.externalHost.current.querySelectorAll<HTMLElement>(
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
  return { ready, error };
}
