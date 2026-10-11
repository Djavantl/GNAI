import { useId, type RefObject } from "react";
import Button from "@/components/UI/Button/Button";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import type { CalendarCategory } from "@/components/UI/EventCalendar/EventCalendar.types";
import "./CalendarCategories.css";
export default function CalendarCategories({
  host,
  categories,
  removeAfterDrop,
  onRemoveChange,
  onCreate,
}: {
  host: RefObject<HTMLDivElement | null>;
  categories: CalendarCategory[];
  removeAfterDrop: boolean;
  onRemoveChange: (checked: boolean) => void;
  onCreate: () => void;
}): React.JSX.Element {
  const id = useId();
  return (
    <div className="calendarCategories">
      <Button tone="default" size="lg" block onClick={onCreate}>
        <i className="fa fa-plus" aria-hidden="true" /> Criar novo
      </Button>
      <div ref={host} className="calendarCategories__events">
        <br />
        <p>Arraste e solte seu evento ou clique no calendário</p>
        {categories.map((category) => (
          <div
            key={category.id}
            className={`calendarCategories__event calendarCategories__event--${category.tone}`}
            data-category-id={category.id}
            data-title={category.title}
            data-tone={category.tone}
          >
            {category.title}
          </div>
        ))}
      </div>
      <ChoiceControl
        type="checkbox"
        id={id}
        label="Remover após soltar"
        checked={removeAfterDrop}
        onChange={onRemoveChange}
        className="calendarCategories__remove"
      />
    </div>
  );
}
