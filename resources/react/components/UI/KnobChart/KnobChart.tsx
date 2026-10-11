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
  animate?: boolean;
}
export default function KnobChart({
  value,
  options,
  className,
  animate = false,
}: KnobChartProps): React.JSX.Element {
  const mount = useCallback<ChartMount>(
    (element) => {
      const input = document.createElement("input");
      const shouldAnimate = animate && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      input.value = String(shouldAnimate ? 0 : value);
      input.setAttribute("aria-label", `${value}%`);
      element.append(input);
      const $ = chartWindow.jQuery;
      const control = $(input);
      control.knob(options);
      const animation = $({ value: 0 });
      if (shouldAnimate) {
        animation.animate({ value }, {
          duration: 1000,
          easing: 'swing',
          step: (current: number) => control.val(Math.round(current)).trigger('change'),
          complete: () => control.val(value).trigger('change'),
        });
      }
      return {
        destroy: () => {
          animation.stop(true, false);
          $(input).trigger("destroy");
          $(input).off();
          $(element).find("canvas").off();
        },
      };
    },
    [value, options, animate],
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
