import PeityChart from "@/Components/UI/PeityChart/PeityChart";
import "./DashboardTwoMiniMetricCard.css";
import type { DashboardTwoMiniMetricItem } from "./DashboardTwoMiniMetricCard.types";
import CardBox from "@/Components/UI/CardBox/CardBox";

interface DashboardTwoMiniMetricCardProps {
  data: DashboardTwoMiniMetricItem;
}

export default function DashboardTwoMiniMetricCard({
  data,
}: DashboardTwoMiniMetricCardProps): JSX.Element {
  return (
    <CardBox className="dashboardTwoMiniMetricCard">
      <div className="dashboardTwoMiniMetricCard__barWidget bar-widget">
        <div className="dashboardTwoMiniMetricCard__table table-box">
          <div className="dashboardTwoMiniMetricCard__cell table-detail">
            <div
              className={`dashboardTwoMiniMetricCard__iconbox iconbox bg-${data.colorTone}`}
            >
              <i
                className={data.iconClass ?? "icon-layers"}
                aria-hidden="true"
              />
            </div>
          </div>

          <div className="dashboardTwoMiniMetricCard__cell table-detail">
            <h4 className="dashboardTwoMiniMetricCard__value m-t-0 m-b-5">
              <b>{data.value}</b>
            </h4>
            <h5 className="dashboardTwoMiniMetricCard__title text-muted m-b-0 m-t-0">
              {data.title}
            </h5>
          </div>

          <div className="dashboardTwoMiniMetricCard__cell dashboardTwoMiniMetricCard__cell--right table-detail text-right">
            {data.chartType === "bars" && data.barValues ? (
              <PeityChart
                type="bar"
                values={data.barValues.join(",")}
                options={{
                  fill: ["#34d3eb", "#ebeff2"],
                  width: 120,
                  height: 45,
                }}
              />
            ) : null}
            {data.chartType === "pie" && data.fraction ? (
              <PeityChart
                type="pie"
                values={`${data.fraction.value}/${data.fraction.total}`}
                options={{
                  fill: ["#5fbeaa", "#ebeff2"],
                  width: 50,
                  height: 45,
                }}
              />
            ) : null}
            {data.chartType === "donut" && data.fraction ? (
              <PeityChart
                type="donut"
                values={`${data.fraction.value}/${data.fraction.total}`}
                options={{
                  fill: ["#f05050", "#ebeff2"],
                  width: 50,
                  height: 45,
                }}
              />
            ) : null}
          </div>
        </div>
      </div>
    </CardBox>
  );
}
