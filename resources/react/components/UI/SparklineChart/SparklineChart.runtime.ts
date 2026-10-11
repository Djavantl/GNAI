import { loadExternalScript } from "@/utils/loadExternalScript";
import type {
  SparklineChartConfig,
  SparklineMouseSpeedConfig,
} from "./SparklineChart.types";

interface JQuerySparklineCollection {
  width(): number;
  sparkline(values: number[], options: Record<string, unknown>): void;
}

export interface JQueryRuntime {
  (element: Element): JQuerySparklineCollection;
}

interface SparklineWindow extends Window {
  jQuery?: JQueryRuntime;
  $?: JQueryRuntime;
}

export async function loadSparklineRuntime(
  chartElement: Element | null,
  isCancelled: () => boolean,
): Promise<JQueryRuntime | null> {
  await loadExternalScript("/assets/react/js/jquery.min.js");
  await loadExternalScript(
    "/assets/react/plugins/jquery-sparkline/jquery.sparkline.min.js",
  );

  if (isCancelled() || !chartElement) {
    return null;
  }

  const runtimeWindow = window as SparklineWindow;
  return runtimeWindow.jQuery ?? runtimeWindow.$ ?? null;
}

export function drawSparklineChart(
  runtime: JQueryRuntime,
  chartElement: Element,
  chart: SparklineChartConfig,
): void {
  const sparkline = runtime(chartElement);

  if (chart.chartType === "pie") {
    sparkline.sparkline(chart.pieValues, {
      type: "pie",
      width: String(chart.chartHeight),
      height: String(chart.chartHeight),
      sliceColors: chart.pieColors,
    });
  }

  if (chart.chartType === "bar") {
    sparkline.sparkline(chart.barValues, {
      type: "bar",
      height: String(chart.chartHeight),
      barWidth: chart.barWidth ?? "10",
      barSpacing: chart.barSpacing ?? "3",
      barColor: chart.barColor ?? "#fb6d9d",
    });
  }

  if (chart.chartType === "line") {
    chart.lineSeries.forEach((series) => {
      sparkline.sparkline(series.values, {
        type: "line",
        width: String(sparkline.width()),
        height: String(chart.chartHeight),
        chartRangeMax: series.rangeMax,
        lineColor: series.color,
        fillColor: series.fillColor ?? "transparent",
        composite: series.composite,
        highlightLineColor:
          series.highlightLineColor ??
          (series.composite ? "rgba(0,0,0,1)" : "rgba(0,0,0,.1)"),
        highlightSpotColor:
          series.highlightSpotColor ??
          (series.composite ? "rgba(0,0,0,1)" : "rgba(0,0,0,.2)"),
      });
    });
  }

  if (chart.chartType === "bar-line") {
    sparkline.sparkline(chart.barValues, {
      type: "bar",
      height: String(chart.chartHeight),
      barWidth: chart.barWidth ?? "10",
      barSpacing: chart.barSpacing ?? "3",
      barColor: chart.barColor,
    });

    sparkline.sparkline(chart.lineValues, {
      type: "line",
      width: String(sparkline.width()),
      height: String(chart.chartHeight),
      chartRangeMax: chart.rangeMax,
      lineColor: chart.lineColor,
      fillColor: chart.fillColor,
      composite: true,
      highlightLineColor: chart.highlightLineColor,
      highlightSpotColor: chart.highlightSpotColor,
    });
  }
}

export function drawMouseSpeedSparkline(
  runtime: JQueryRuntime,
  chartElement: Element,
  chart: SparklineMouseSpeedConfig,
  mousePoints: number[],
): void {
  runtime(chartElement).sparkline(mousePoints, {
    tooltipSuffix: chart.tooltipSuffix,
    type: "line",
    width: String(runtime(chartElement).width()),
    height: String(chart.chartHeight),
    chartRangeMax: chart.rangeMax,
    lineColor: chart.lineColor,
    fillColor: chart.fillColor,
    highlightLineColor: chart.highlightLineColor,
    highlightSpotColor: chart.highlightSpotColor,
  });
}
