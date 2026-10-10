import Table from '@/Components/Tables/Table/Table';
import Label from '@/Components/UI/Label/Label';
import type { DashboardThreeProjectItem, DashboardThreeProjectsColumns } from './DashboardThreeProjectsPortlet.types';
import { useDashboardThreeProjectsPortlet } from './useDashboardThreeProjectsPortlet';

interface DashboardThreeProjectsPortletProps {
  columns?: DashboardThreeProjectsColumns;
  items: DashboardThreeProjectItem[];
  title?: string;
}

const defaultColumns: DashboardThreeProjectsColumns = {
  id: '#',
  projectName: 'Nome do projeto',
  startDate: 'Data de início',
  dueDate: 'Prazo',
  status: 'Status',
  assign: 'Responsável'
};

export default function DashboardThreeProjectsPortlet({
  columns = defaultColumns,
  items,
  title = 'Projetos'
}: DashboardThreeProjectsPortletProps): JSX.Element {
  const {
    collapseRef,
    collapsed,
    refreshing,
    removing,
    removed,
    collapseHeight,
    handleReload,
    handleCollapse,
    handleRemove,
    handleRemoveTransitionEnd
  } = useDashboardThreeProjectsPortlet(items);

  if (removed) {
    return <></>;
  }

  return (
    <div
      className={`dashboardThreeProjectsPortlet portlet${collapsed ? ' dashboardThreeProjectsPortlet--collapsed' : ''}${
        removing ? ' dashboardThreeProjectsPortlet--removing' : ''
      }`}
      onTransitionEnd={handleRemoveTransitionEnd}
    >
      <div className="dashboardThreeProjectsPortlet__heading portlet-heading">
        <h3 className="dashboardThreeProjectsPortlet__title portlet-title text-dark text-uppercase">{title}</h3>
        <div className="dashboardThreeProjectsPortlet__widgets portlet-widgets">
          <button type="button" aria-label="Recarregar" onClick={handleReload}>
            <i className="ion-refresh" />
          </button>
          <span className="divider" />
          <button
            type="button"
            className={collapsed ? 'collapsed' : ''}
            aria-label={collapsed ? 'Expandir' : 'Recolher'}
            onClick={handleCollapse}
          >
            <i className={collapsed ? 'ion-plus-round' : 'ion-minus-round'} />
          </button>
          <span className="divider" />
          <button type="button" aria-label="Fechar" onClick={handleRemove}>
            <i className="ion-close-round" />
          </button>
        </div>
        <div className="clearfix" />
      </div>

      <div
        ref={collapseRef}
        className="dashboardThreeProjectsPortlet__collapse panel-collapse"
        style={{ height: collapseHeight === 'auto' ? 'auto' : `${collapseHeight}px` }}
      >
        <div className="dashboardThreeProjectsPortlet__body portlet-body">
          <div className="table-responsive">
            <Table appearance="inherit" className="dashboardThreeProjectsPortlet__table table">
              <thead>
                <tr>
                  <th>{columns.id}</th>
                  <th>{columns.projectName}</th>
                  <th>{columns.startDate}</th>
                  <th>{columns.dueDate}</th>
                  <th>{columns.status}</th>
                  <th>{columns.assign}</th>
                </tr>
              </thead>
              <tbody>
                {items.map((item) => (
                  <tr key={item.id}>
                    <td>{item.id}</td>
                    <td>{item.projectName}</td>
                    <td>{item.startDate}</td>
                    <td>{item.dueDate}</td>
                    <td>
                      <Label variant={item.statusVariant}>{item.status}</Label>
                    </td>
                    <td>{item.assign}</td>
                  </tr>
                ))}
              </tbody>
            </Table>
          </div>
        </div>
      </div>

      {refreshing ? (
        <div className="dashboardThreeProjectsPortlet__disabled panel-disabled" aria-hidden="true">
          <div className="loader-1" />
        </div>
      ) : null}
    </div>
  );
}
