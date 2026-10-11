import { Fragment, useId, useState } from "react";
import "./SortButtonGroup.css";

export default function SortButtonGroup({
  options,
  type,
  initialSelected = [],
  label = "Ordenar por:",
}: {
  options: string[];
  type: "checkbox" | "radio";
  initialSelected?: string[];
  label?: string;
}): React.JSX.Element {
  const id = useId();
  const [selected, setSelected] = useState<string[]>(initialSelected);
  return (
    <div className="sortButtonGroup">
      <span>{label}</span>{" "}
      <div className="sortButtonGroup__buttons">
        {options.map((option) => (
          <Fragment key={option}>
            <label
              className={`sortButtonGroup__button waves-effect${selected.includes(option) ? " is-active" : ""}`}
            >
              <input
                type={type}
                name={id}
                checked={selected.includes(option)}
                onChange={(event) =>
                  setSelected((current) =>
                    type === "radio"
                      ? [option]
                      : event.target.checked
                        ? [...current, option]
                        : current.filter((item) => item !== option),
                  )
                }
              />
              {option}
            </label>
          </Fragment>
        ))}
      </div>
    </div>
  );
}
