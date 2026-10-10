import type { SparklineChartConfig } from "@/Components/UI/SparklineChart/SparklineChart.types";
const first = [0, 23, 43, 35, 44, 45, 56, 37, 40];
const second = [25, 23, 26, 24, 25, 32, 30, 24, 19];
const bars = [3, 6, 7, 8, 6, 4, 7, 10, 12, 7, 4, 9, 12, 13, 11, 12];
export const sparklineExamples: Record<string, SparklineChartConfig> = {
  sparkline1: {
    chartType: "line",
    chartHeight: 165,
    lineSeries: [
      {
        values: first,
        color: "#5fbeaa",
        fillColor: "rgba(95, 190, 170, 0.3)",
        rangeMax: 50,
      },
      {
        values: second,
        color: "#5d9cec",
        fillColor: "rgba(93, 156, 236, 0.3)",
        rangeMax: 40,
        composite: true,
        highlightLineColor: "rgba(0,0,0,.1)",
        highlightSpotColor: "rgba(0,0,0,.2)",
      },
    ],
  },
  sparkline2: {
    chartType: "bar",
    chartHeight: 165,
    barValues: bars,
    barColor: "#fb6d9d",
  },
  sparkline3: {
    chartType: "pie",
    chartHeight: 165,
    pieValues: [20, 40, 30, 10],
    pieColors: ["#dcdcdc", "#5d9cec", "#36404a", "#5fbeaa"],
  },
  sparkline4: {
    chartType: "line",
    chartHeight: 165,
    lineSeries: [
      { values: first, color: "#fb6d9d", rangeMax: 50 },
      { values: second, color: "#5d9cec", rangeMax: 40, composite: true },
    ],
  },
  sparkline5: {
    chartType: "mouse-speed",
    chartHeight: 165,
    lineColor: "#1e88e5",
    fillColor: "rgba(30, 136, 229, 0.3)",
    rangeMax: 50,
    highlightLineColor: "rgba(24,147,126,.1)",
    highlightSpotColor: "rgba(24,147,126,.2)",
    tooltipSuffix: " pixels per second",
  },
  sparkline6: {
    chartType: "bar-line",
    chartHeight: 165,
    barValues: bars,
    barColor: "#5fbeaa",
    lineValues: bars,
    lineColor: "#fb6d9d",
    fillColor: "transparent",
    highlightLineColor: "rgba(0,0,0,.1)",
    highlightSpotColor: "rgba(0,0,0,.2)",
  },
};
