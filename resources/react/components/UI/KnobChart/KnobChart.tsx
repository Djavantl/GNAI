import { useCallback } from "react";
import { chartWindow } from "@/utils/chartRuntime";
import type { ChartMount } from "@/hooks/useChart.types";
import { useChart } from "@/hooks/useChart";
const scripts = [
  "/assets/react/js/jquery.min.js",
  "/assets/react/plugins/jquery-knob/jquery.knob.js",
];
export interface KnobChartProps {
  value: number;
  options: Record<string, unknown>;
  className?: string;
}
export default function KnobChart({
  value,
  options,
  className,
}: KnobChartProps): React.JSX.Element {
  const mount = useCallback<ChartMount>(
    (element) => {
      const input = document.createElement("input");
      input.value = String(value);
      input.setAttribute("aria-label", `${value}%`);
      element.append(input);
      const $ = chartWindow.jQuery;
      $(input).knob(options);
      return {
        destroy: () => {
          $(input).trigger("destroy");
          $(input).off();
          $(element).find("canvas").off();
        },
      };
    },
    [value, options],
  );
  const ref = useChart(scripts, mount);
  return (
    <div
      ref={ref}
      className={className}
      style={{ display: "inline" }}
    />
  );
}
