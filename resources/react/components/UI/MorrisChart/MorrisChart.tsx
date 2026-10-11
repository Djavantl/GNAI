import './MorrisChart.css';
import { useCallback } from "react";
import { chartWindow } from "@/utils/chartRuntime";
import type { ChartMount } from "@/hooks/useChart.types";
import { useChart } from "@/hooks/useChart";

export interface MorrisChartDatum {
  [key: string]: string | number;
}

interface MorrisChartBaseOptions {
  data: MorrisChartDatum[];
  xkey: string;
  ykeys: string[];
  labels: string[];
  hideHover?: "auto";
  resize?: boolean;
  gridLineColor?: string;
}

export interface MorrisBarChartOptions extends MorrisChartBaseOptions {
  stacked?: boolean;
  barColors: string[];
}

export interface MorrisAreaChartOptions extends MorrisChartBaseOptions {
  pointFillColors?: string[];
  pointStrokeColors?: string[];
  lineColors: string[];
  pointSize?: number;
  lineWidth?: number;
}

export interface MorrisChartProps {
  className?: string;
  label?: string;
  options:
    MorrisBarChartOptions | MorrisAreaChartOptions | Record<string, unknown>;
  type: "bar" | "area" | "line" | "donut";
  height?: number;
}
const scripts = [
  "/assets/react/js/jquery.min.js",
  "/assets/react/plugins/raphael/raphael-min.js",
  "/assets/react/plugins/morris/morris.min.js",
];
export default function MorrisChart({
  className,
  options,
  type,
  height,
  label,
}: MorrisChartProps): React.JSX.Element {
  const mount = useCallback<ChartMount>(
    (element) => {
      const runtime = chartWindow.Morris;
      const constructor = type[0].toUpperCase() + type.slice(1);
      const chart = new runtime[constructor]({
        ...options,
        element,
        resize: false,
      });
      return {
        resize: () => chart.redraw(),
        destroy: () => {
          chartWindow.jQuery(element).off();
          chart.raphael?.remove();
        },
      };
    },
    [options, type],
  );
  const ref = useChart(scripts, mount);
  return (
    <div
      ref={ref}
      className={className}
      style={height ? { height } : undefined}
      aria-label={label ?? `${type} chart`}
    />
  );
}
