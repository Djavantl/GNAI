import "./DashboardTwoRevenueCard.css";
import type {
  DashboardTwoRevenueLegendItem,
  DashboardTwoRevenueProgressItem,
  DashboardTwoRevenueSeriesItem,
} from "./DashboardTwoRevenueCard.types";
import CardBox from "@/components/UI/CardBox/CardBox";
import ChartLegend from "@/components/UI/ChartLegend/ChartLegend";
import MorrisChart, {
  type MorrisAreaChartOptions,
} from "@/components/UI/MorrisChart/MorrisChart";

interface DashboardTwoRevenueCardProps {
  title: string;
  legend: DashboardTwoRevenueLegendItem[];
  series: DashboardTwoRevenueSeriesItem[];
  progressItems: DashboardTwoRevenueProgressItem[];
}

export default function DashboardTwoRevenueCard({
  title,
  legend,
  series,
  progressItems,
}: DashboardTwoRevenueCardProps): React.JSX.Element {
  const chartOptions: MorrisAreaChartOptions = {
    pointSize: 0,
    lineWidth: 0,
    data: series.map((item: DashboardTwoRevenueSeriesItem) => ({
      y: item.year,
      a: item.desktop,
      b: item.tablet,
      c: item.mobile,
    })),
    xkey: "y",
    ykeys: ["a", "b", "c"],
    labels: legend.map((item: DashboardTwoRevenueLegendItem) => item.label),
    hideHover: "auto",
    pointFillColors: ["#ffffff"],
    pointStrokeColors: ["#999999"],
    resize: true,
    gridLineColor: "#eef0f2",
    lineColors: legend.map((item: DashboardTwoRevenueLegendItem) => item.color),
  };

  return (
    <CardBox className="dashboardTwoRevenueCard">
      <h4 className="dashboardTwoRevenueCard__title text-dark header-title m-t-0">
        {title}
      </h4>

      <div className="dashboardTwoRevenueCard__row">
        <div className="dashboardTwoRevenueCard__chartColumn">
          <ChartLegend items={legend} />

          <MorrisChart
            type="area"
            options={chartOptions}
            className="dashboardTwoRevenueCard__plot"
            label={`${title} por ano`}
          />
        </div>

        <div className="dashboardTwoRevenueCard__progressColumn">
          {progressItems.map((item: DashboardTwoRevenueProgressItem) => (
            <div
              key={item.label}
              className="dashboardTwoRevenueCard__progressGroup"
            >
              <p className="dashboardTwoRevenueCard__progressLabel font-600">
                {item.label}{" "}
                <span className={`pull-right text-${item.tone}`}>
                  {item.value}%
                </span>
              </p>
              <div className="dashboardTwoRevenueCard__progressTrack progress m-b-30">
                <div
                  className={`dashboardTwoRevenueCard__progressBar progress-bar progress-bar-${item.tone}`}
                  role="progressbar"
                  aria-valuenow={item.value}
                  aria-valuemin={0}
                  aria-valuemax={100}
                  style={{ width: `${item.value}%` }}
                />
              </div>
            </div>
          ))}
        </div>
      </div>
    </CardBox>
  );
}
