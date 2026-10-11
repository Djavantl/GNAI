import { forwardRef, useEffect, useImperativeHandle, useRef } from "react";
import { loadExternalScript } from "@/utils/loadExternalScript";

type PeityChartType = "pie" | "line" | "bar" | "donut";

interface JQueryPeityCollection {
  removeData(key: string): JQueryPeityCollection;
  off(event: string): void;
  peity(type: PeityChartType, options?: Record<string, unknown>): void;
}

interface JQueryRuntime {
  (element: Element): JQueryPeityCollection;
}

interface PeityWindow extends Window {
  jQuery?: JQueryRuntime;
  $?: JQueryRuntime;
}

interface PeityChartProps {
  className?: string;
  options?: Record<string, unknown>;
  type: PeityChartType;
  values: string;
  updating?: boolean;
}

const PeityChart = forwardRef<HTMLSpanElement, PeityChartProps>(
  function PeityChart(
    { className, options, type, values, updating = false }: PeityChartProps,
    forwardedRef,
  ): React.JSX.Element {
    const chartRef = useRef<HTMLSpanElement | null>(null);

    useImperativeHandle(
      forwardedRef,
      () => chartRef.current as HTMLSpanElement,
      [],
    );

    useEffect(() => {
      let cancelled = false;
      let timer: number | undefined;
      const element = chartRef.current;

      async function mountChart(): Promise<void> {
        await loadExternalScript("/assets/react/js/jquery.min.js");
        await loadExternalScript("/assets/react/plugins/peity/jquery.peity.min.js");

        if (cancelled || !chartRef.current) {
          return;
        }

        const runtimeWindow = window as PeityWindow;
        const runtime = runtimeWindow.jQuery ?? runtimeWindow.$;

        if (!runtime) {
          return;
        }

        runtime(chartRef.current).peity(type, options);
        if (updating)
          timer = window.setInterval(() => {
            if (!element) return;
            const points = (element.textContent ?? values).split(",");
            points.shift();
            points.push(String(Math.round(Math.random() * 10)));
            element.textContent = points.join(",");
            runtime(element).peity(type, options);
          }, 1000);
      }

      void mountChart();

      return () => {
        cancelled = true;
        window.clearInterval(timer);
        element?.nextElementSibling?.matches("svg.peity") &&
          element.nextElementSibling.remove();
        if (element)
          (window as PeityWindow)
            .jQuery?.(element)
            .removeData("_peity")
            .off("change");
      };
    }, [options, type, values, updating]);

    return (
      <span ref={chartRef} className={className}>
        {values}
      </span>
    );
  },
);

export default PeityChart;
