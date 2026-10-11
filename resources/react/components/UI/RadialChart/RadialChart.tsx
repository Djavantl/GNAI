import { useState } from "react";
import "./RadialChart.css";
export interface RadialChartProps {
  value: number;
  label?: string;
  size?: "lg" | "sm" | "xs";
  tone?: string;
  image?: string;
  tooltip?: string;
}
export default function RadialChart({
  value,
  label,
  size,
  tone,
  image,
  tooltip,
}: RadialChartProps): React.JSX.Element {
  const [visible, setVisible] = useState(false);
  return (
    <div
      className={`radialChart radial-bar radial-bar-${value}${size ? ` radial-bar-${size}` : ""}${tone ? ` radial-bar-${tone}` : ""}`}
      data-label={label}
      tabIndex={tooltip ? 0 : undefined}
      aria-label={tooltip ?? label}
      onMouseEnter={() => setVisible(true)}
      onMouseLeave={() => setVisible(false)}
      onFocus={() => setVisible(true)}
      onBlur={() => setVisible(false)}
    >
      {image && <img src={image} alt="Image" />}
      {tooltip && (
        <span
          role="tooltip"
          aria-hidden={!visible}
          className={`radialChart__tooltip${visible ? " radialChart__tooltip--visible" : ""}`}
        >
          {tooltip}
        </span>
      )}
    </div>
  );
}
