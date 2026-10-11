import { useRef, useState } from "react";
import CardBox from "@/components/UI/CardBox/CardBox";
import CalendarCategories from "@/components/UI/CalendarCategories/CalendarCategories";
import { initialCategories } from "@/components/UI/CalendarCategories/CalendarCategories.data";
import EventCalendar from "@/components/UI/EventCalendar/EventCalendar";
import CalendarDialogs from "@/components/UI/CalendarDialogs/CalendarDialogs";
import type {
  CalendarApi,
  CalendarCategory,
} from "@/components/UI/EventCalendar/EventCalendar.types";
import type { CalendarDialog } from "@/components/UI/CalendarDialogs/CalendarDialogs.types";
import "./CalendarPage.css";
export default function CalendarPage(): React.JSX.Element {
  const externalHost = useRef<HTMLDivElement>(null);
  const api = useRef<CalendarApi | null>(null);
  const [categories, setCategories] =
    useState<CalendarCategory[]>(initialCategories);
  const [removeAfterDrop, setRemoveAfterDrop] = useState(false);
  const [dialog, setDialog] = useState<CalendarDialog | null>(null);
  const [dialogVersion, setDialogVersion] = useState(0);
  function open(next: CalendarDialog): void {
    setDialog(next);
    setDialogVersion((value) => value + 1);
  }
  return (
    <div className="calendarPage">
      <aside className="calendarPage__categories">
        <CalendarCategories
          host={externalHost}
          categories={categories}
          removeAfterDrop={removeAfterDrop}
          onRemoveChange={setRemoveAfterDrop}
          onCreate={() => open({ kind: "category" })}
        />
      </aside>
      <div className="calendarPage__calendar">
        <CardBox>
          <EventCalendar
            externalHost={externalHost}
            categories={categories}
            api={api}
            onSelect={(selection) => open({ kind: "selection", selection })}
            onEventClick={(event) => open({ kind: "event", event })}
            onExternalDrop={(id) => {
              if (removeAfterDrop)
                setCategories((values) =>
                  values.filter((category) => category.id !== id),
                );
            }}
          />
        </CardBox>
      </div>
      <CalendarDialogs
        key={dialogVersion}
        dialog={dialog}
        onClose={() => setDialog(null)}
        onCreateCategory={(title, tone) =>
          setCategories((values) => [
            ...values,
            { id: `category-${Date.now()}`, title, tone },
          ])
        }
        onCreateEvent={(selection, title, tone) =>
          api.current?.create({
            ...selection,
            title,
            className: `bg-${tone}`,
            allDay: false,
          })
        }
        onUpdateEvent={(event, title) => api.current?.update(event, title)}
        onDeleteEvent={(event) => api.current?.remove(event)}
      />
    </div>
  );
}
