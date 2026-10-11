import './DashboardOne.css';
import PageHeader from '@/components/Navigation/PageHeader/PageHeader';
import StatWidgetCard from '@/components/UI/StatWidgetCard/StatWidgetCard';
import type { DashboardStatItem } from './DashboardOne.data';
import { dashboardStats } from './DashboardOne.data';
import RevenueSummaryCard from '@/components/UI/RevenueSummaryCard/RevenueSummaryCard';
import { dashboardRevenueSummary } from './DashboardOne.revenue.data';
import SalesAnalyticsCard from '@/components/UI/SalesAnalyticsCard/SalesAnalyticsCard';
import { salesAnalyticsData } from '@/components/UI/SalesAnalyticsCard/SalesAnalyticsCard.data';
import TotalSalesCard from '@/components/UI/TotalSalesCard/TotalSalesCard';
import { totalSalesData } from '@/components/UI/TotalSalesCard/TotalSalesCard.data';
import RecentOrdersCard from '@/components/UI/RecentOrdersCard/RecentOrdersCard';
import { recentOrdersData } from '@/components/UI/RecentOrdersCard/RecentOrdersCard.data';

export default function DashboardOne(): React.JSX.Element {
  return (
    <section className="dashboardOne" aria-label="Dashboard 1">
      <div className="dashboardOne__row">
        <div className="dashboardOne__col dashboardOne__col--full">
          <PageHeader title="Dashboard" subtitle="Bem-vindo ao painel administrativo interface!" />
        </div>
      </div>

      <div className="dashboardOne__row">
        {dashboardStats.map((item: DashboardStatItem) => (
          <div key={item.title} className="dashboardOne__col dashboardOne__col--stat">
            <StatWidgetCard
              iconClass={item.iconClass}
              title={item.title}
              value={item.value}
              tone={item.tone}
              animatedClassName={item.animatedClassName}
            />
          </div>
        ))}
      </div>

      <div className="dashboardOne__row">
        <div className="dashboardOne__col dashboardOne__col--revenue">
          <RevenueSummaryCard
            title={dashboardRevenueSummary.title}
            caption={dashboardRevenueSummary.caption}
            amount={dashboardRevenueSummary.amount}
            progress={dashboardRevenueSummary.progress}
            metrics={dashboardRevenueSummary.metrics}
          />
        </div>

        <div className="dashboardOne__col dashboardOne__col--analytics">
          <SalesAnalyticsCard data={salesAnalyticsData} />
        </div>
      </div>

      <div className="dashboardOne__row">
        <div className="dashboardOne__col dashboardOne__col--half">
          <TotalSalesCard data={totalSalesData} />
        </div>

        <div className="dashboardOne__col dashboardOne__col--half">
          <RecentOrdersCard data={recentOrdersData} />
        </div>
      </div>
    </section>
  );
}
