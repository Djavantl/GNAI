import { useCustomScrollbar } from '@/hooks/useCustomScrollbar';
import './DashboardTwoTransactionsCard.css';
import type { DashboardTwoTransactionItem } from './DashboardTwoTransactionsCard.types';
import CardBox from '@/Components/UI/CardBox/CardBox';

interface DashboardTwoTransactionsCardProps {
  title: string;
  items: DashboardTwoTransactionItem[];
}

export default function DashboardTwoTransactionsCard({
  title,
  items
}: DashboardTwoTransactionsCardProps): JSX.Element {
  const { scrollRef, thumbHeight, thumbTop, isVisible } = useCustomScrollbar<HTMLDivElement>(true);

  return (
    <CardBox className="dashboardTwoTransactionsCard">
      <h4 className="dashboardTwoTransactionsCard__title m-t-0 m-b-20 header-title">
        <b>{title}</b>
      </h4>

      <div className="dashboardTwoTransactionsCard__body">
        <div
          ref={scrollRef}
          className="dashboardTwoTransactionsCard__scrollRegion nicescroll mx-box"
        >
        <ul className="dashboardTwoTransactionsCard__list list-unstyled transaction-list m-r-5">
          {items.map((item: DashboardTwoTransactionItem) => (
            <li key={`${item.title}-${item.date}`}>
              <i className={item.iconClass} aria-hidden="true" />
              <span className="dashboardTwoTransactionsCard__text tran-text">{item.title}</span>
              <span className={`pull-right ${item.amountToneClass} tran-price`}>{item.amount}</span>
              <span className="pull-right text-muted">{item.date}</span>
              <span className="clearfix" />
            </li>
          ))}
        </ul>
        </div>

        <div
          className={`dashboardTwoTransactionsCard__scrollbar ${isVisible && thumbHeight > 0 ? 'dashboardTwoTransactionsCard__scrollbar--visible' : ''}`}
          aria-hidden="true"
        >
          <div
            className="dashboardTwoTransactionsCard__scrollbarThumb"
            style={{
              height: `${thumbHeight}px`,
              transform: `translateY(${thumbTop}px)`
            }}
          />
        </div>
      </div>
    </CardBox>
  );
}
