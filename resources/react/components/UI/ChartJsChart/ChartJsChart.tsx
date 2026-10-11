import { useCallback } from "react";
import { chartWindow } from "@/utils/chartRuntime";
import type { ChartMount } from "@/hooks/useChart.types";
import { useChart } from "@/hooks/useChart";
const scripts = ["/assets/react/plugins/Chart.js/Chart.min.js"];
export interface ChartJsChartProps {
  type: "Line" | "Bar" | "Pie" | "Doughnut" | "Radar" | "PolarArea";
  data: unknown;
  options?: Record<string, unknown>;
  height?: number;
}
export default function ChartJsChart({
  type,
  data,
  options,
  height = 300,
}: ChartJsChartProps): React.JSX.Element {
  const mount = useCallback<ChartMount>(
    (element) => {
      const canvas = document.createElement("canvas");
      canvas.height = height;
      element.append(canvas);
      let chart: any;
      const draw = (): void => {
        chart?.destroy();
        canvas.width = element.clientWidth;
        canvas.height = height;
        chart = new chartWindow.Chart(canvas.getContext("2d"))[type](
          structuredClone(data),
          options,
        );
      };
      draw();
      return { resize: draw, destroy: () => chart?.destroy() };
    },
    [type, data, options, height],
  );
  const ref = useChart(scripts, mount);
  return <div ref={ref} />;
}
