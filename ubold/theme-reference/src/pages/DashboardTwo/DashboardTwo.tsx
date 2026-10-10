import { useState } from 'react';
import PageHeader from '@/Components/Navigation/PageHeader/PageHeader';
import DashboardTwoStatCard from '@/Components/UI/DashboardTwoStatCard/DashboardTwoStatCard';
import DashboardTwoRevenueCard from '@/Components/UI/DashboardTwoRevenueCard/DashboardTwoRevenueCard';
import DashboardTwoMiniMetricCard from '@/Components/UI/DashboardTwoMiniMetricCard/DashboardTwoMiniMetricCard';
import DashboardTwoTransactionsCard from '@/Components/UI/DashboardTwoTransactionsCard/DashboardTwoTransactionsCard';
import DashboardTwoChatCard from '@/Components/UI/DashboardTwoChatCard/DashboardTwoChatCard';
import DashboardTwoTodoCard from '@/Components/UI/DashboardTwoTodoCard/DashboardTwoTodoCard';
import SweetAlertOops from '@/Components/UI/SweetAlertOops/SweetAlertOops';
import './DashboardTwo.css';
import {
  dashboardTwoInitialMessages,
  dashboardTwoInitialTodos,
  dashboardTwoMiniMetrics,
  dashboardTwoRevenueData,
  dashboardTwoStats,
  dashboardTwoTransactions
} from './DashboardTwo.data';

export default function DashboardTwo(): JSX.Element {
  const [oopsMessage, setOopsMessage] = useState<string | null>(null);

  return (
    <section className="dashboardTwo" aria-label="Dashboard 2">
      <div className="dashboardTwo__row">
        <div className="dashboardTwo__col dashboardTwo__col--full">
          <PageHeader title="Dashboard 2" subtitle="Bem-vindo ao painel administrativo Bold!" />
        </div>
      </div>

      <div className="dashboardTwo__row">
        {dashboardTwoStats.map((item) => (
          <div key={item.title} className="dashboardTwo__col dashboardTwo__col--stat">
            <DashboardTwoStatCard iconClass={item.iconClass} title={item.title} value={item.value} />
          </div>
        ))}
      </div>

      <div className="dashboardTwo__row">
        <div className="dashboardTwo__col dashboardTwo__col--full">
          <DashboardTwoRevenueCard
            title={dashboardTwoRevenueData.title}
            legend={dashboardTwoRevenueData.legend}
            series={dashboardTwoRevenueData.series}
            progressItems={dashboardTwoRevenueData.progressItems}
          />
        </div>
      </div>

      <div className="dashboardTwo__row">
        {dashboardTwoMiniMetrics.map((item, index: number) => (
          <div key={`${item.title}-${index}`} className="dashboardTwo__col dashboardTwo__col--third">
            <DashboardTwoMiniMetricCard data={item} />
          </div>
        ))}
      </div>

      <div className="dashboardTwo__row dashboardTwo__row--bottomCards">
        <div className="dashboardTwo__col dashboardTwo__col--third">
          <DashboardTwoTransactionsCard title="Últimas transações" items={dashboardTwoTransactions} />
        </div>

        <div className="dashboardTwo__col dashboardTwo__col--third">
          <DashboardTwoChatCard title="Chat" initialMessages={dashboardTwoInitialMessages} onEmptyMessage={() => setOopsMessage('Você esqueceu de digitar sua mensagem do bate-papo')} />
        </div>

        <div className="dashboardTwo__col dashboardTwo__col--third">
          <DashboardTwoTodoCard title="Tarefas" initialItems={dashboardTwoInitialTodos} onEmptyTodo={() => setOopsMessage('Você esqueceu de digitar o texto da tarefa')} />
        </div>
      </div>
      {oopsMessage ? <SweetAlertOops message={oopsMessage} onClose={() => setOopsMessage(null)} /> : null}
    </section>
  );
}
