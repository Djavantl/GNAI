import { chartWindow } from "@/utils/chartRuntime";
import { forwardRef, useEffect, useImperativeHandle, useRef } from "react";
import {
  drawMouseSpeedSparkline,
  drawSparklineChart,
  loadSparklineRuntime,
  type JQueryRuntime,
} from "./SparklineChart.runtime";
import type {
  SparklineChartConfig,
  SparklineLineSeries,
} from "./SparklineChart.types";

interface SparklineChartProps {
  chart: SparklineChartConfig;
  className?: string;
  onDraw?: () => void;
}

const SparklineChart = forwardRef<HTMLDivElement, SparklineChartProps>(
  function SparklineChart(
    { chart, className, onDraw }: SparklineChartProps,
    forwardedRef,
  ): React.JSX.Element {
    const chartRef = useRef<HTMLDivElement | null>(null);

    const pointer = useRef<{ x: number; y: number } | null>(null);

    useImperativeHandle(
      forwardedRef,
      () => chartRef.current as HTMLDivElement,
      [],
    );

    useEffect(() => {
      const element = chartRef.current;
      let cancelled = false;
      let resizeTimer = 0;
      let mouseSpeedTimer = 0;
      let redrawMouseSpeed: (() => void) | null = null;
      let cleanupMouseSpeed: (() => void) | null = null;

      function syncPointer(): void {
        const point = pointer.current;
        const canvas = chartRef.current?.querySelector("canvas");
        if (point && canvas)
          canvas.dispatchEvent(
            new MouseEvent("mousemove", {
              bubbles: true,
              clientX: point.x,
              clientY: point.y,
            }),
          );
      }

      async function prepareSparkline(): Promise<JQueryRuntime | null> {
        return loadSparklineRuntime(chartRef.current, () => cancelled);
      }

      async function draw(): Promise<void> {
        const runtime = await prepareSparkline();

        if (!runtime || cancelled || !chartRef.current) {
          return;
        }

        const chartElement = chartRef.current;
        chartElement.innerHTML = "";
        drawSparklineChart(runtime, chartElement, chart);

        syncPointer();
        onDraw?.();
      }

      async function startMouseSpeed(): Promise<void> {
        const runtime = await prepareSparkline();

        if (
          !runtime ||
          cancelled ||
          !chartRef.current ||
          chart.chartType !== "mouse-speed"
        ) {
          return;
        }

        const chartElement = chartRef.current;
        let lastMouseX = -1;
        let lastMouseY = -1;
        let lastMouseTime: number | undefined;
        let mouseTravel = 0;
        const mousePoints: number[] = [];
        const maxMousePoints = 30;
        const refreshInterval = chart.refreshInterval ?? 500;

        function handleMouseMove(event: MouseEvent): void {
          if (lastMouseX > -1) {
            mouseTravel += Math.max(
              Math.abs(event.pageX - lastMouseX),
              Math.abs(event.pageY - lastMouseY),
            );
          }

          lastMouseX = event.pageX;
          lastMouseY = event.pageY;
        }

        function drawMouseSpeedChart(): void {
          if (cancelled || chart.chartType !== "mouse-speed") {
            return;
          }

          drawMouseSpeedSparkline(
            runtime as JQueryRuntime,
            chartElement,
            chart,
            mousePoints,
          );
          syncPointer();
          onDraw?.();
        }

        function drawMouseSpeed(): void {
          if (cancelled) {
            return;
          }

          const now = new Date().getTime();
          if (lastMouseTime && lastMouseTime !== now) {
            const pixelsPerSecond = Math.round(
              (mouseTravel / (now - lastMouseTime)) * 1000,
            );
            mousePoints.push(pixelsPerSecond);

            if (mousePoints.length > maxMousePoints) {
              mousePoints.splice(0, 1);
            }

            mouseTravel = 0;
            drawMouseSpeedChart();
          }

          lastMouseTime = now;
          mouseSpeedTimer = window.setTimeout(drawMouseSpeed, refreshInterval);
        }

        redrawMouseSpeed = drawMouseSpeedChart;
        document.documentElement.addEventListener("mousemove", handleMouseMove);
        mouseSpeedTimer = window.setTimeout(drawMouseSpeed, refreshInterval);

        cleanupMouseSpeed = () => {
          document.documentElement.removeEventListener(
            "mousemove",
            handleMouseMove,
          );
        };
      }

      if (chart.chartType === "mouse-speed") {
        void startMouseSpeed();
      } else {
        void draw();
      }

      function handleResize(): void {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(() => {
          if (chart.chartType === "mouse-speed") {
            redrawMouseSpeed?.();
            return;
          }

          void draw();
        }, 300);
      }

      window.addEventListener("resize", handleResize);

      return () => {
        cancelled = true;
        window.clearTimeout(resizeTimer);
        window.clearTimeout(mouseSpeedTimer);
        window.removeEventListener("resize", handleResize);
        cleanupMouseSpeed?.();
        if (element && chartWindow.jQuery) {
          const host = chartWindow.jQuery(element);
          const handler = host.data("_jqs_mhandler");
          if (handler?.over) handler.mouseleave();
          host.find("canvas").off();
          host.removeData("_jqs_mhandler _jqs_vcanvas _jqs_pending");
          element.replaceChildren();
        }
      };
    }, [chart, onDraw]);

    return (
      <div
        ref={chartRef}
        className={className}
        aria-hidden="true"
        onMouseMove={(event) => {
          pointer.current = { x: event.clientX, y: event.clientY };
        }}
        onMouseLeave={() => {
          pointer.current = null;
        }}
      />
    );
  },
);

export type { SparklineChartConfig, SparklineLineSeries };
export default SparklineChart;
