import type { RefObject } from "react";
export type CalendarTone =
  | "success"
  | "danger"
  | "info"
  | "pink"
  | "primary"
  | "warning"
  | "inverse"
  | "purple";
export interface CalendarDate {
  format: (format?: string) => string;
}
export interface CalendarEvent {
  id?: string;
  _id?: string;
  sessionDate?: string;
  title: string;
  start: Date | CalendarDate | string;
  end?: Date | CalendarDate | string;
  allDay?: boolean;
  className: string | string[];
}
export interface CalendarSelection {
  start: CalendarDate;
  end: CalendarDate;
}
export type CalendarViewName = "month" | "agendaWeek" | "agendaDay";
export interface CalendarViewChange {
  name: CalendarViewName;
  date: string;
  start: string;
  end: string;
}
export interface CalendarCategory {
  id: string;
  title: string;
  tone: CalendarTone;
}
export interface CalendarCollection {
  fullCalendar: (
    options: Record<string, unknown> | string,
    ...args: unknown[]
  ) => CalendarCollection;
  draggable: (
    options: Record<string, unknown> | "destroy",
  ) => CalendarCollection;
  data: (key: string) => unknown;
}
export interface CalendarRuntime {
  (element: Element): CalendarCollection;
}
export interface CalendarApi {
  create: (event: CalendarEvent) => void;
  update: (event: CalendarEvent, title: string) => void;
  remove: (event: CalendarEvent) => void;
  goToDate: (date: string) => void;
  replace: (events: CalendarEvent[]) => void;
}
export interface EventCalendarProps {
  externalHost?: RefObject<HTMLDivElement | null>;
  categories?: CalendarCategory[];
  initialEvents?: CalendarEvent[];
  initialDate?: string;
  defaultView?: CalendarViewName;
  selectedDate?: string;
  height?: number | string;
  eventLimit?: boolean | number;
  /** Primeiro horário das visões de semana/dia (HH:mm:ss). */
  minTime?: string;
  /** Último horário das visões de semana/dia (HH:mm:ss). */
  maxTime?: string;
  allDaySlot?: boolean;
  /** Quando falso, eventos simultâneos dividem a coluna lado a lado. */
  slotEventOverlap?: boolean;
  header?: false | { left: string; center: string; right: string };
  editable?: boolean;
  droppable?: boolean;
  selectable?: boolean;
  ariaLabel?: string;
  className?: string;
  onDayClick?: (date: string) => void;
  onMoreClick?: (date: string) => void;
  /** Clique no cabeçalho de um dia nas visões de semana/dia. */
  onDayHeaderClick?: (date: string) => void;
  onViewChange?: (view: CalendarViewChange) => void;
  onSelect?: (selection: CalendarSelection) => void;
  onEventClick?: (event: CalendarEvent) => void;
  onExternalDrop?: (categoryId: string) => void;
  api?: RefObject<CalendarApi | null>;
}
