import CardBox from '@/components/UI/CardBox/CardBox';
import MorrisChart from '@/components/UI/MorrisChart/MorrisChart';
import '@/components/UI/SalesAnalyticsCard/SalesAnalyticsCard.css';
import type { Metric } from './Overview.types';

export const colors: Record<string, string> = {
  primary: '#5d9cec', secondary: '#98a6ad', info: '#34d3eb', success: '#81c868',
  warning: '#ffbd4a', danger: '#f05050', purple: '#7266ba',
};
const palette = ['#5fbeaa', '#5d9cec', '#ebeff2', '#7266ba', '#fb6d9d'];
export default function MetricsChart({ title, items, total }: {
  title: string; items: Metric[]; total?: number;
}): React.JSX.Element {
  const hasData = items.some(item => item.count > 0);

  return <CardBox className="salesAnalyticsCard">
    <h2 className="salesAnalyticsCard__title text-dark header-title">{title}</h2>
    <ul className="salesAnalyticsCard__legend list-inline chart-detail-list" aria-label={title}>
      {items.map((item, index) => <li className="salesAnalyticsCard__legendItem" key={item.label}>
        <h5 className="salesAnalyticsCard__legendLabel"><i className="fa fa-circle salesAnalyticsCard__legendIcon" style={{ color: palette[index % palette.length] }} />{item.label}: {item.count.toLocaleString('pt-BR')}</h5>
      </li>)}
    </ul>
    {hasData ? <MorrisChart type="donut" height={303} label={title} options={{
      data: items.map(item => ({ label: item.label, value: item.count })),
      colors: palette, resize: true,
    }} /> : <p className="gnaiDashboard__empty">Nenhum registro para exibir.</p>}
    {total !== undefined && <p className="gnaiDashboard__total">Total geral <strong>{total.toLocaleString('pt-BR')}</strong></p>}
  </CardBox>;
}
