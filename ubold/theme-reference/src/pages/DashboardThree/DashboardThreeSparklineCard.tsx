import type { DashboardThreeSparklineCardData } from './DashboardThreeSparklineCard.types';
import CardBox from '@/Components/UI/CardBox/CardBox';
import SparklineChart, { type SparklineChartConfig } from '@/Components/UI/SparklineChart/SparklineChart';

interface DashboardThreeSparklineCardProps {
  data: DashboardThreeSparklineCardData;
}

export default function DashboardThreeSparklineCard({ data }: DashboardThreeSparklineCardProps): JSX.Element {
  const chartConfig: SparklineChartConfig =
    data.chartType === 'pie' && data.pieValues && data.pieColors
      ? {
          chartType: 'pie',
          chartHeight: data.chartHeight,
          pieValues: data.pieValues,
          pieColors: data.pieColors
        }
      : data.chartType === 'bar' && data.barValues
        ? {
            chartType: 'bar',
            chartHeight: data.chartHeight,
            barValues: data.barValues,
            barWidth: data.barWidth,
            barSpacing: data.barSpacing,
            barColor: data.barColor
          }
        : {
            chartType: 'line',
            chartHeight: data.chartHeight,
            lineSeries: data.lineSeries ?? []
          };

  return (
    <CardBox className="dashboardThreeSparklineCard">
      <h4 className="dashboardThreeSparklineCard__title text-dark header-title">{data.title}</h4>

      <div
        className="dashboardThreeSparklineCard__content widget-chart text-center"
      >
        <SparklineChart
          chart={chartConfig}
          className="dashboardThreeSparklineCard__chart"
        />

        <ul className="dashboardThreeSparklineCard__metrics list-inline">
          {data.metrics.map((metric) => (
            <li key={metric.label}>
              <h5 className="dashboardThreeSparklineCard__metricLabel text-muted">{metric.label}</h5>
              <h4 className="dashboardThreeSparklineCard__metricValue m-b-0">{metric.value}</h4>
            </li>
          ))}
        </ul>
      </div>
    </CardBox>
  );
}
