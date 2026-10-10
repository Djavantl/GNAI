import { useCallback } from "react";
import { chartWindow } from "@/utils/chartRuntime";
import type { ChartMount } from "@/hooks/useChart.types";
import { useChart } from "@/hooks/useChart";
import "./ChartistChart.css";
export type ChartistFactory = (
  element: HTMLElement,
  runtime: any,
  jquery: any,
  schedule: (callback: () => void, delay: number) => number,
) => any;
const scripts = [
  "/assets/gnai/js/jquery.min.js",
  "/assets/gnai/plugins/chartist/dist/chartist.min.js",
];
export default function ChartistChart({
  create,
  className = "",
}: {
  create: ChartistFactory;
  className?: string;
}): JSX.Element {
  const mount = useCallback<ChartMount>(
    (element) => {
      const timers = new Set<number>();
      const schedule = (callback: () => void, delay: number): number => {
        const id = window.setTimeout(() => {
          timers.delete(id);
          callback();
        }, delay);
        timers.add(id);
        return id;
      };
      const chart = create(
        element,
        chartWindow.Chartist,
        chartWindow.jQuery,
        schedule,
      );
      return {
        resize: () => chart.update(),
        destroy: () => {
          timers.forEach((id) => window.clearTimeout(id));
          chart.detach();
          chartWindow.jQuery(element).off();
        },
      };
    },
    [create],
  );
  const ref = useChart(scripts, mount);
  return (
    <div ref={ref} className={`ct-chart ct-golden-section ${className}`} />
  );
}
