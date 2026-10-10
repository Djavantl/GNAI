import CardBox from "@/Components/UI/CardBox/CardBox";
import PeityChart from "@/Components/UI/PeityChart/PeityChart";
import type { DealStat } from "./DealStatCard.types";
import "./DealStatCard.css";
export default function DealStatCard({
  stat,
}: {
  stat: DealStat;
}): JSX.Element {
  return (
    <CardBox>
      <article className="dealStatCard">
        <div>
          <span
            className="dealStatCard__icon"
            style={{ backgroundColor: stat.color }}
          >
            <i className={stat.icon} aria-hidden="true" />
          </span>
        </div>
        <div className="dealStatCard__value">
          <h4>
            <b>{stat.title}</b>
          </h4>
          <h5>{stat.count} negócios</h5>
        </div>
        <div className="dealStatCard__chart">
          <PeityChart
            type={stat.chart}
            options={stat.options}
            values={stat.values}
          />
        </div>
      </article>
    </CardBox>
  );
}
