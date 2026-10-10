import { useCallback } from "react";
import { chartWindow } from "@/utils/chartRuntime";
import type { ChartMount } from "@/hooks/useChart.types";
import { useChart } from "@/hooks/useChart";
import "./C3Chart.css";
const scripts = [
  "/assets/gnai/plugins/d3/d3.min.js",
  "/assets/gnai/plugins/c3/c3.min.js",
];
export default function C3Chart({
  options,
  label,
}: {
  options: Record<string, unknown>;
  label?: string;
}): JSX.Element {
  const mount = useCallback<ChartMount>(
    (element) => {
      const chart = chartWindow.c3.generate({ ...options, bindto: element });
      return { resize: () => chart.resize(), destroy: () => chart.destroy() };
    },
    [options],
  );
  const ref = useChart(scripts, mount);
  return <div ref={ref} role={label ? "group" : undefined} aria-label={label} />;
}
