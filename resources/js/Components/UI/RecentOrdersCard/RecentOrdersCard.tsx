import './RecentOrdersCard.css';
import type { RecentOrdersData, RecentOrdersItem, RecentOrdersSparkline } from './RecentOrdersCard.data';
import Button from '@/Components/UI/Button/Button';
import DataTable from '@/Components/Tables/DataTable/DataTable';
import CardBox from '@/Components/UI/CardBox/CardBox';
import PeityChart from '@/Components/UI/PeityChart/PeityChart';

interface RecentOrdersCardProps {
  data: RecentOrdersData;
}

function getSparklineOptions(sparkline: RecentOrdersSparkline): Record<string, unknown> {
  if (sparkline.type === 'line') {
    return {
      fill: sparkline.fill,
      stroke: sparkline.stroke,
      width: sparkline.width,
      height: sparkline.height
    };
  }

  return {
    fill: sparkline.colors,
    width: sparkline.width,
    height: sparkline.height
  };
}

export default function RecentOrdersCard({ data }: RecentOrdersCardProps): JSX.Element {
  return (
    <CardBox className="recentOrdersCard">
      <Button href="#" label={data.actionLabel} size="sm" className="recentOrdersCard__action pull-right" />
      <h4 className="recentOrdersCard__title text-dark header-title m-t-0">{data.title}</h4>
      <p className="recentOrdersCard__description text-muted">{data.description}</p>

      <DataTable className="recentOrdersCard__table table-actions-bar m-b-0">
          <thead>
            <tr>
              <th />
              <th>{data.columns.product}</th>
              <th>{data.columns.change}</th>
              <th style={{ minWidth: 80 }}>{data.columns.manage}</th>
            </tr>
          </thead>
          <tbody>
            {data.items.map((item: RecentOrdersItem) => (
              <tr key={item.id}>
                <td>
                  <PeityChart
                    type={item.sparkline.type}
                    values={item.sparkline.values}
                    options={getSparklineOptions(item.sparkline)}
                  />
                </td>
                <td>
                  <img src={item.productImage} className="recentOrdersCard__thumb thumb-sm pull-left m-r-10" alt="" /> {item.productName}
                </td>
                <td>
                  <span
                    className={
                      item.trendTone === 'positive' ? 'recentOrdersCard__trend text-custom' : 'recentOrdersCard__trend text-danger'
                    }
                  >
                    {item.trendText}
                  </span>
                </td>
                <td>
                  <a href="#" className="recentOrdersCard__tableAction table-action-btn">
                    <i className="md md-edit" />
                  </a>
                  <a href="#" className="recentOrdersCard__tableAction table-action-btn">
                    <i className="md md-close" />
                  </a>
                </td>
              </tr>
            ))}
          </tbody>
      </DataTable>
    </CardBox>
  );
}
