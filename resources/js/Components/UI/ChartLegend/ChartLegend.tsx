import { Fragment } from "react";
import "./ChartLegend.css";
export interface ChartLegendItem {
  label: string;
  color: string;
}
export default function ChartLegend({
  items,
}: {
  items: ChartLegendItem[];
}): JSX.Element {
  return (
    <div className="chartLegend">
      <ul>
        {items.map((item, index) => (
          <Fragment key={item.label}>
            {index > 0 ? " " : null}
            <li>
              <h5>
                <i
                  className="fa fa-circle"
                  style={{ color: item.color }}
                  aria-hidden="true"
                />
                {item.label}
              </h5>
            </li>
          </Fragment>
        ))}
      </ul>
    </div>
  );
}
