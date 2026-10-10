import type { DashboardThreeMetricItem } from './DashboardThreeMetricCard.types';
import CardBox from '@/Components/UI/CardBox/CardBox';
import PeityChart from '@/Components/UI/PeityChart/PeityChart';

interface DashboardThreeMetricCardProps {
  data: DashboardThreeMetricItem;
}

export default function DashboardThreeMetricCard({ data }: DashboardThreeMetricCardProps): JSX.Element {
  return (
    <CardBox className={`dashboardThreeMetricCard dashboardThreeMetricCard--${data.key}`}>
      <div className="dashboardThreeMetricCard__widget bar-widget">
        <div className="dashboardThreeMetricCard__table table-box">
          <div className="dashboardThreeMetricCard__cell table-detail">
            <div className={`dashboardThreeMetricCard__iconbox iconbox bg-${data.colorTone}`}>
              <i className={data.iconClass ?? 'icon-layers'} aria-hidden="true" />
            </div>
          </div>

          <div className="dashboardThreeMetricCard__cell table-detail">
            <h4 className="dashboardThreeMetricCard__value m-t-0 m-b-5">
              <b>{data.value}</b>
            </h4>
            <p className="dashboardThreeMetricCard__title text-muted m-b-0 m-t-0">{data.title}</p>
          </div>

          <div className="dashboardThreeMetricCard__cell dashboardThreeMetricCard__cell--chart table-detail text-right">
            <PeityChart
              type={data.chartType}
              values={data.chartValues}
              options={{
                fill: data.chartColors,
                width: String(data.chartWidth),
                height: String(data.chartHeight)
              }}
            />
          </div>
        </div>
      </div>
    </CardBox>
  );
}
