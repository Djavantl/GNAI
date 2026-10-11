import './TotalSalesCard.css';
import type { TotalSalesData, TotalSalesLegendItem } from './TotalSalesCard.data';
import CardBox from '@/components/UI/CardBox/CardBox';
import MorrisChart, { type MorrisAreaChartOptions } from '@/components/UI/MorrisChart/MorrisChart';

interface TotalSalesCardProps {
  data: TotalSalesData;
}

export default function TotalSalesCard({ data }: TotalSalesCardProps): React.JSX.Element {
  const chartOptions: MorrisAreaChartOptions = {
    data: data.series.map((item) => ({
      y: item.year,
      a: item.desktop,
      b: item.tablet,
      c: item.mobile
    })),
    xkey: 'y',
    ykeys: ['a', 'b', 'c'],
    labels: data.legend.map((item: TotalSalesLegendItem) => item.label),
    hideHover: 'auto',
    pointFillColors: ['#ffffff'],
    pointStrokeColors: ['#999999'],
    resize: true,
    gridLineColor: '#eef0f2',
    lineColors: data.legend.map((item: TotalSalesLegendItem) => item.color),
    pointSize: 0,
    lineWidth: 0
  };

  return (
    <CardBox className="totalSalesCard">
      <h4 className="totalSalesCard__title text-dark header-title">{data.title}</h4>

      <div className="text-center">
        <ul className="totalSalesCard__legend list-inline chart-detail-list">
          {data.legend.map((item: TotalSalesLegendItem) => (
            <li key={item.label} className="totalSalesCard__legendItem">
              <h5 className="totalSalesCard__legendLabel">
                <i className="fa fa-circle totalSalesCard__legendIcon" style={{ color: item.color }} />
                {item.label}
              </h5>
            </li>
          ))}
        </ul>
      </div>

      <MorrisChart type="area" options={chartOptions} className="totalSalesCard__plot" />
    </CardBox>
  );
}
