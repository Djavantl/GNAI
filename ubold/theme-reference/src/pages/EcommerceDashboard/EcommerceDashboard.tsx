import DashboardTwoStatCard from "@/Components/UI/DashboardTwoStatCard/DashboardTwoStatCard";
import DashboardTwoRevenueCard from "@/Components/UI/DashboardTwoRevenueCard/DashboardTwoRevenueCard";
import ProductOrdersTable from "@/Components/Tables/ProductOrdersTable/ProductOrdersTable";
import CardBox from "@/Components/UI/CardBox/CardBox";
import Button from "@/Components/UI/Button/Button";
import {
  stats,
  revenueLegend,
  revenueProgress,
  revenueSeries,
  dashboardTables,
} from "./EcommerceDashboard.data";
import "./EcommerceDashboard.css";
export default function EcommerceDashboard(): JSX.Element {
  return (
    <div className="ecommerceDashboard">
      <div className="ecommerceDashboard__stats">
        {stats.map((stat) => (
          <div key={stat.title}>
            <DashboardTwoStatCard {...stat} />
          </div>
        ))}
      </div>
      <DashboardTwoRevenueCard
        title="Receita total"
        legend={revenueLegend}
        series={revenueSeries}
        progressItems={revenueProgress}
      />
      <div className="ecommerceDashboard__tables">
        {dashboardTables.map((table) => (
          <div key={table.title}>
            <CardBox>
              <Button
                href="#"
                tone="default"
                size="sm"
                className="ecommerceDashboard__viewAll"
                label="Ver todos"
              />
              <h4 className="ecommerceDashboard__title">{table.title}</h4>
              <p className="ecommerceDashboard__description">
                Seu texto incrível aparece aqui.
              </p>
              <ProductOrdersTable
                records={table.records}
                variant={table.variant}
              />
            </CardBox>
          </div>
        ))}
      </div>
    </div>
  );
}
