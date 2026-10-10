import type { CalendarEvent } from "./EventCalendar.types";
export function createDefaultCalendarEvents(now = Date.now()): CalendarEvent[] {
  return [
    { title: "Olá!", start: new Date(now + 158000000), className: "bg-purple" },
    {
      title: "Encontrar John Deo",
      start: new Date(now),
      end: new Date(now),
      className: "bg-danger",
    },
    {
      title: "Comprar um tema",
      start: new Date(now + 338000000),
      className: "bg-primary",
    },
  ];
}
