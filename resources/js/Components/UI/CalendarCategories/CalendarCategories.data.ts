import type {
  CalendarCategory,
  CalendarTone,
} from "@/Components/UI/EventCalendar/EventCalendar.types";
export const initialCategories: CalendarCategory[] = [
  { id: "one", title: "Meu evento um", tone: "primary" },
  { id: "two", title: "Meu evento dois", tone: "pink" },
  { id: "three", title: "Meu evento três", tone: "info" },
  { id: "four", title: "Meu evento quatro", tone: "purple" },
];
export const calendarColors: { value: CalendarTone; label: string }[] = [
  { value: "success", label: "Sucesso" },
  { value: "danger", label: "Perigo" },
  { value: "info", label: "Informação" },
  { value: "pink", label: "Rosa" },
  { value: "primary", label: "Primária" },
  { value: "warning", label: "Aviso" },
  { value: "inverse", label: "Inversa" },
  { value: "purple", label: "Roxa" },
];
