import "./FlotChart.css";
import { useCallback } from "react";
import { chartWindow } from "@/utils/chartRuntime";
import type { ChartMount } from "@/hooks/useChart.types";
import { useChart } from "@/hooks/useChart";
export interface FlotChartProps {
  data: unknown[];
  options: Record<string, unknown>;
  realtime?: boolean;
  height?: number;
}
const scripts = [
  "/assets/gnai/js/jquery.min.js",
  ...[
    "jquery.flot.js",
    "jquery.flot.time.js",
    "jquery.flot.tooltip.min.js",
    "jquery.flot.resize.js",
    "jquery.flot.pie.js",
    "jquery.flot.selection.js",
    "jquery.flot.stack.js",
    "jquery.flot.orderBars.min.js",
    "jquery.flot.crosshair.js",
  ].map((name) => "/assets/gnai/plugins/flot-chart/" + name),
];
export default function FlotChart({
  data,
  options,
  realtime = false,
  height = 320,
}: FlotChartProps): JSX.Element {
  const mount = useCallback<ChartMount>(
    (element) => {
      const $ = chartWindow.jQuery;
      const plot = $.plot($(element), structuredClone(data), options);
      let values = realtime
        ? (data[0] as number[][]).map((point) => point[1])
        : [];
      const tick = (): void => {
        values = values.slice(1);
        values.push(
          Math.max(
            0,
            Math.min(100, values[values.length - 1] + Math.random() * 10 - 5),
          ),
        );
        plot.setData([values.map((value, index) => [index, value])]);
        plot.draw();
      };
      const timer = realtime ? window.setInterval(tick, 1000) : undefined;
      return {
        resize: () => {
          plot.resize();
          plot.setupGrid();
          plot.draw();
        },
        destroy: () => {
          window.clearInterval(timer);
          plot.shutdown();
          $(element).off();
        },
      };
    },
    [data, options, realtime],
  );
  const ref = useChart(scripts, mount);
  return <div ref={ref} className="flot-chart" style={{ height }} />;
}
