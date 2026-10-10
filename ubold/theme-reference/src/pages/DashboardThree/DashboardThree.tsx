import './DashboardThree.css';
import PageHeader from '@/Components/Navigation/PageHeader/PageHeader';
import DashboardThreeMetricCard from './DashboardThreeMetricCard';
import DashboardThreeProjectsPortlet from './DashboardThreeProjectsPortlet';
import DashboardThreeSparklineCard from './DashboardThreeSparklineCard';
import {
  dashboardThreeMetrics,
  dashboardThreeProjects,
  dashboardThreeSparklineCards
} from './DashboardThree.data';

export default function DashboardThree(): JSX.Element {
  return (
    <section className="dashboardThree" aria-label="Dashboard 3">
      <div className="dashboardThree__row">
        <div className="dashboardThree__col dashboardThree__col--full">
          <PageHeader title="Dashboard 3" subtitle="Bem-vindo ao painel administrativo Bold!" />
        </div>
      </div>

      <div className="dashboardThree__row">
        {dashboardThreeMetrics.map((item) => (
          <div key={item.title} className="dashboardThree__col dashboardThree__col--third">
            <DashboardThreeMetricCard data={item} />
          </div>
        ))}
      </div>

      <div className="dashboardThree__row">
        {dashboardThreeSparklineCards.map((item) => (
          <div key={item.title} className="dashboardThree__col dashboardThree__col--third">
            <DashboardThreeSparklineCard data={item} />
          </div>
        ))}
      </div>

      <div className="dashboardThree__row">
        <div className="dashboardThree__col dashboardThree__col--full">
          <DashboardThreeProjectsPortlet items={dashboardThreeProjects} />
        </div>
      </div>
    </section>
  );
}
