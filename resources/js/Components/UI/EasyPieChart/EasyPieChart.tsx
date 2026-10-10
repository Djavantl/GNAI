import { useCallback } from "react";
import { chartWindow } from "@/utils/chartRuntime";
import type { ChartMount } from "@/hooks/useChart.types";
import { useChart } from "@/hooks/useChart";
import "./EasyPieChart.css";
const scripts = [
  "/assets/gnai/js/jquery.min.js",
  "/assets/gnai/plugins/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js",
];
export default function EasyPieChart({
  percent,
  options,
}: {
  percent: number;
  options: Record<string, unknown>;
}): JSX.Element {
  const mount = useCallback<ChartMount>(
    (element) => {
      element.dataset.percent = String(percent);
      const label = document.createElement("span");
      label.className = "percent";
      element.append(label);
      const $ = chartWindow.jQuery;
      $(element).easyPieChart({
        ...options,
        onStep: (_from: number, _to: number, value: number) => {
          label.textContent = String(Math.round(value));
        },
      });
      return {
        destroy: () => {
          $(element).removeData("easyPieChart");
        },
      };
    },
    [percent, options],
  );
  const ref = useChart(scripts, mount);
  return <div ref={ref} className="easyPieChart chart" />;
}
