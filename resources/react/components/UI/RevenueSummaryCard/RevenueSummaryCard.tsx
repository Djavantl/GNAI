import KnobChart from "@/components/UI/KnobChart/KnobChart";
import "./RevenueSummaryCard.css";

interface RevenueMetric {
  label: string;
  value: string;
}

interface RevenueSummaryCardProps {
  title: string;
  caption: string;
  amount: string;
  progress: number;
  metrics: RevenueMetric[];
}

export default function RevenueSummaryCard({
  title,
  caption,
  amount,
  progress,
  metrics,
}: RevenueSummaryCardProps): React.JSX.Element {
  return (
    <div className="revenueSummaryCard card-box">
      <h4 className="revenueSummaryCard__title text-dark header-title">
        {title}
      </h4>

      <div className="revenueSummaryCard__content widget-chart text-center">
        <div className="revenueSummaryCard__chart" aria-hidden="true">
          <KnobChart
            value={progress}
            options={{
              width: 150,
              height: 150,
              lineCap: "round",
              fgColor: "#fb6d9d",
              skin: "tron",
              angleOffset: 180,
              readOnly: true,
              thickness: 0.15,
            }}
          />
        </div>

        <h5 className="revenueSummaryCard__caption text-muted">{caption}</h5>
        <h2 className="revenueSummaryCard__amount font-600">{amount}</h2>

        <ul className="revenueSummaryCard__metrics list-inline">
          {metrics.map((metric: RevenueMetric) => (
            <li key={metric.label}>
              <h5 className="revenueSummaryCard__metricLabel text-muted">
                {metric.label}
              </h5>
              <h4 className="revenueSummaryCard__metricValue m-b-0">
                {metric.value}
              </h4>
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
}
