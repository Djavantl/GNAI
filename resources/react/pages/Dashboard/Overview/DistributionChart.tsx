import CardBox from '@/components/UI/CardBox/CardBox';
import C3Chart from '@/components/UI/C3Chart/C3Chart';
import type { Metric } from './Overview.types';

// Models from the Ubold C3 examples: Rotated Chart and Donut Chart.
export default function DistributionChart({ title, items, variant }: {
  title: string; items: Metric[]; variant: 'rotated' | 'donut';
}): React.JSX.Element {
  const total = items.reduce((sum, item) => sum + item.count, 0);
  const options = variant === 'rotated' ? {
    data: {
      columns: [['Quantidade', ...items.map(item => item.count)]],
      type: 'bar', colors: { Quantidade: '#5fbeaa' },
    },
    axis: {
      rotated: true,
      x: { type: 'category', categories: items.map(item => item.label) },
      y: { min: 0, padding: { bottom: 0 }, tick: { format: (value: number) => Number.isInteger(value) ? String(value) : '' } },
    },
  } : {
    data: { columns: items.map(item => [item.label, item.count]), type: 'donut' },
    donut: { title: `${total.toLocaleString('pt-BR')} agendamentos`, width: 15, label: { show: false } },
    color: { pattern: ['#5fbeaa', '#5d9cec', '#f4f8fb', '#7266ba', '#fb6d9d'] },
  };
  return <CardBox className="salesAnalyticsCard">
    <h2 className="salesAnalyticsCard__title text-dark header-title">{title}</h2>
    {total > 0 ? <C3Chart label={`${title}: ${items.map(item => `${item.label}: ${item.count}`).join('; ')}`} options={{ ...options, size: { height: 303 } }} />
      : <p className="gnaiDashboard__empty">Nenhum registro para exibir.</p>}
  </CardBox>;
}
