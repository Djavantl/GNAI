import './DashboardTwoStatCard.css';

interface DashboardTwoStatCardProps {
  iconClass: string;
  title: string;
  value: string;
}

export default function DashboardTwoStatCard({
  iconClass,
  title,
  value
}: DashboardTwoStatCardProps): React.JSX.Element {
  return (
    <div className="dashboardTwoStatCard widget-panel widget-style-2 bg-white">
      <i className={iconClass} aria-hidden="true" />
      <h2 className="dashboardTwoStatCard__value m-0 text-dark counter font-600">{value}</h2>
      <div className="dashboardTwoStatCard__title text-muted m-t-5">{title}</div>
    </div>
  );
}
