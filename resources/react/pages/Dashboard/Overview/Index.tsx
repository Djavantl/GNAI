import { Head, Link, usePage } from '@inertiajs/react';
import PageHeader from '@/components/Navigation/PageHeader/PageHeader';
import StatWidgetCard from '@/components/UI/StatWidgetCard/StatWidgetCard';
import MetricsChart from './MetricsChart';
import DistributionChart from './DistributionChart';
import RevenueSummaryCard from '@/components/UI/RevenueSummaryCard/RevenueSummaryCard';
import BarrierMap from './BarrierMap';
import type { OverviewProps } from './Overview.types';
import './Overview.css';

const support = '/specialized-educational-support';
const radar = '/inclusive-radar';
export default function Overview({ specializedEducationalSupport: s, inclusiveRadar: r }: OverviewProps): React.JSX.Element {
  const { auth } = usePage<{ auth: { user: { name?: string; email: string } | null } }>().props;
  const stats = [
    { title: 'Alunos', value: s.totalStudents, href: `${support}/students`, icon: 'users', tone: 'info' },
    { title: 'Equipe', value: s.totalProfessionals, href: `${support}/professionals`, icon: 'user-md', tone: 'purple' },
    { title: 'PEIs finalizados', value: s.totalPeisFinished, href: `${support}/peis?is_finished=1`, icon: 'check-square-o', tone: 'success' },
    { title: 'Agendamentos', value: s.totalSessions, href: `${support}/sessions`, icon: 'calendar', tone: 'info' },
    { title: 'Pendências', value: s.totalPendingPendencies, href: `${support}/pendencies?is_completed=0`, icon: 'exclamation-circle', tone: 'pink' },
    { title: 'Atrasadas', value: s.totalOverduePendencies, href: `${support}/pendencies?is_completed=0`, icon: 'clock-o', tone: 'purple' },
  ] as const;
  const radarStats = [
    { title: 'Tecnologias assistivas', value: r.totalAt, href: 'assistive-technologies', icon: 'desktop', tone: 'purple' },
    { title: 'Materiais pedagógicos acessíveis', value: r.totalAem, href: 'accessible-educational-materials', icon: 'book', tone: 'info' },
    { title: 'Empréstimos', value: r.totalLoans, href: 'loans', icon: 'exchange', tone: 'success' },
    { title: 'Fila de espera: aguardando/notificados', value: r.totalWaiting, href: 'waitlists', icon: 'hourglass-half', tone: 'pink' },
  ] as const;
  return <section className="gnaiDashboard" aria-label="Dashboard GNAI">
    <Head title="Dashboard" />
    <PageHeader title="Dashboard" subtitle={`Bem-vindo(a) ao GNAI${auth.user ? `, ${auth.user.name || auth.user.email}` : ''}.`} />
    <div className="gnaiDashboard__stats">{stats.map(item => <Link key={item.title} href={item.href} aria-label={`${item.title}: ${item.value}`}><StatWidgetCard title={item.title} value={String(item.value)} tone={item.tone} iconClass={`fa fa-${item.icon} text-${item.tone}`} /></Link>)}</div>
    <div className="gnaiDashboard__charts">
      <RevenueSummaryCard
        title="Status dos PEIs"
        caption={s.totalPeis ? 'Percentual de PEIs finalizados' : 'Nenhum PEI cadastrado'}
        progress={s.totalPeis ? Math.round(s.totalPeisFinished / s.totalPeis * 100) : 0}
        amount={`${s.totalPeis ? Math.round(s.totalPeisFinished / s.totalPeis * 100) : 0}%`}
        metrics={[
          { label: 'Finalizados', value: s.totalPeisFinished.toLocaleString('pt-BR') },
          { label: 'Não finalizados', value: s.totalPeisNotFinished.toLocaleString('pt-BR') },
          { label: 'Total', value: s.totalPeis.toLocaleString('pt-BR') },
        ]}
      />
      <DistributionChart title="Pendências por prioridade" items={s.pendenciesByPriority} variant="rotated" />
    </div>
    <DistributionChart title="Agendamentos por status" items={s.sessionsByStatus} variant="donut" />
    <h2 className="gnaiDashboard__heading">Radar inclusivo</h2>
    <div className="gnaiDashboard__stats gnaiDashboard__stats--radar">{radarStats.map(item => <Link key={item.title} href={`${radar}/${item.href}`} aria-label={`${item.title}: ${item.value}`}><StatWidgetCard title={item.title} value={String(item.value)} tone={item.tone} iconClass={`fa fa-${item.icon} text-${item.tone}`} /></Link>)}</div>
    <div className="gnaiDashboard__charts gnaiDashboard__charts--map">
      <MetricsChart title="Status das barreiras" items={r.barrierStatusCounts} total={r.totalBarriers} />
      <BarrierMap barriers={r.mapBarriers} />
    </div>
  </section>;
}
