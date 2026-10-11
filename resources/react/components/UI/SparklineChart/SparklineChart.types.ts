export interface SparklineLineSeries {
  color: string;
  composite?: boolean;
  fillColor?: string;
  highlightLineColor?: string;
  highlightSpotColor?: string;
  rangeMax: number;
  values: number[];
}

export type SparklineChartConfig =
  | {
      chartHeight: number;
      chartType: "pie";
      pieColors: string[];
      pieValues: number[];
    }
  | {
      barColor?: string;
      barSpacing?: string;
      barValues: number[];
      barWidth?: string;
      chartHeight: number;
      chartType: "bar";
    }
  | {
      chartHeight: number;
      chartType: "line";
      lineSeries: SparklineLineSeries[];
    }
  | {
      barColor: string;
      barSpacing?: string;
      barValues: number[];
      barWidth?: string;
      chartHeight: number;
      chartType: "bar-line";
      fillColor: string;
      highlightLineColor: string;
      highlightSpotColor: string;
      lineColor: string;
      lineValues: number[];
      rangeMax?: number;
    }
  | {
      chartHeight: number;
      chartType: "mouse-speed";
      fillColor: string;
      highlightLineColor: string;
      highlightSpotColor: string;
      lineColor: string;
      rangeMax: number;
      refreshInterval?: number;
      tooltipSuffix: string;
    };

export type SparklineMouseSpeedConfig = Extract<
  SparklineChartConfig,
  { chartType: "mouse-speed" }
>;
