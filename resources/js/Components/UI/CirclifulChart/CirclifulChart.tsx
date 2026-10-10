import { useCallback } from "react";
import { chartWindow } from "@/utils/chartRuntime";
import type { ChartMount } from "@/hooks/useChart.types";
import { useChart } from "@/hooks/useChart";
import "./CirclifulChart.css";
const scripts = [
  "/assets/gnai/js/jquery.min.js",
  "/assets/gnai/plugins/jquery-circliful/js/jquery.circliful.min.js",
];
export default function CirclifulChart({
  attributes,
  className,
}: {
  attributes: Record<string, string>;
  className?: string;
}): JSX.Element {
  const mount = useCallback<ChartMount>(
    (element) => {
      Object.entries(attributes).forEach(([key, value]) =>
        element.setAttribute(key, value),
      );
      chartWindow.jQuery(element).circliful();
      return { destroy: () => chartWindow.jQuery(element).off() };
    },
    [attributes],
  );
  const ref = useChart(scripts, mount);
  return <div ref={ref} className={`circliful-chart ${className ?? ""}`} />;
}
