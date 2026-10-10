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
}
export interface EventCalendarProps {
  externalHost: RefObject<HTMLDivElement>;
  categories: CalendarCategory[];
  initialEvents?: CalendarEvent[];
  onSelect: (selection: CalendarSelection) => void;
  onEventClick: (event: CalendarEvent) => void;
  onExternalDrop: (categoryId: string) => void;
  api: RefObject<CalendarApi | null>;
}
