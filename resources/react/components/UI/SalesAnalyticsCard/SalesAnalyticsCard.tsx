import './SalesAnalyticsCard.css';
import type { SalesAnalyticsData, SalesAnalyticsLegendItem } from './SalesAnalyticsCard.data';
import CardBox from '@/components/UI/CardBox/CardBox';
import MorrisChart, { type MorrisBarChartOptions } from '@/components/UI/MorrisChart/MorrisChart';

interface SalesAnalyticsCardProps {
  data: SalesAnalyticsData;
}

export default function SalesAnalyticsCard({ data }: SalesAnalyticsCardProps): React.JSX.Element {
  const chartOptions: MorrisBarChartOptions = {
    data: data.series.map((item) => ({
      y: item.year,
      a: item.desktop,
      b: item.tablet,
      c: item.mobile
    })),
    xkey: 'y',
    ykeys: ['a', 'b', 'c'],
    labels: data.legend.map((item: SalesAnalyticsLegendItem) => item.label),
    stacked: true,
    hideHover: 'auto',
    resize: true,
    gridLineColor: '#eeeeee',
    barColors: data.legend.map((item: SalesAnalyticsLegendItem) => item.color)
  };

  return (
    <CardBox className="salesAnalyticsCard">
      <h4 className="salesAnalyticsCard__title text-dark header-title">{data.title}</h4>

      <div className="text-center">
        <ul className="salesAnalyticsCard__legend list-inline chart-detail-list">
          {data.legend.map((item: SalesAnalyticsLegendItem) => (
            <li key={item.label} className="salesAnalyticsCard__legendItem">
              <h5 className="salesAnalyticsCard__legendLabel">
                <i className="fa fa-circle salesAnalyticsCard__legendIcon" style={{ color: item.color }} />
                {item.label}
              </h5>
            </li>
          ))}
        </ul>
      </div>

      <MorrisChart type="bar" options={chartOptions} className="salesAnalyticsCard__plot" />
    </CardBox>
  );
}
