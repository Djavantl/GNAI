import type {
  CalendarEvent,
  CalendarSelection,
  CalendarTone,
} from "@/Components/UI/EventCalendar/EventCalendar.types";
export type CalendarDialog =
  | { kind: "category" }
  | { kind: "selection"; selection: CalendarSelection }
  | { kind: "event"; event: CalendarEvent };
export interface CalendarDialogsProps {
  dialog: CalendarDialog | null;
  onClose: () => void;
  onCreateCategory: (title: string, tone: CalendarTone) => void;
  onCreateEvent: (
    selection: CalendarSelection,
    title: string,
    tone: CalendarTone,
  ) => void;
  onUpdateEvent: (event: CalendarEvent, title: string) => void;
  onDeleteEvent: (event: CalendarEvent) => void;
}
