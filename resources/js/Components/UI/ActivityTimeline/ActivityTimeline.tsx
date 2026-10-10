import type { ActivityItem } from "./ActivityTimeline.types";
import "./ActivityTimeline.css";
export default function ActivityTimeline({
  items,
  compactTime = false,
}: {
  items: ActivityItem[];
  compactTime?: boolean;
}): JSX.Element {
  return (
    <div className="activityTimeline">
      {items.map((item, i) => (
        <article className="activityTimeline__item" key={i}>
          <div className="activityTimeline__info">
            <div className="activityTimeline__time">
              {compactTime ? <small>Há {item.minutes} minutos</small> : <>Há {item.minutes} minutos</>}
            </div>
            <p>
              {item.emphasized ? (
                <strong>
                  <a href="#">{item.user}</a>
                </strong>
              ) : (
                <a href="#">{item.user}</a>
              )}{" "}
              {item.action}
              {item.photo && (
                <>
                  {" "}
                  <strong>{item.photo}</strong>
                </>
              )}
              {item.withUser && (
                <>
                  {" "}
                  <a className="activityTimeline__with" href="#">
                    {item.withUser}
                  </a>
                  .
                </>
              )}
            </p>
            {item.quote && (
              <p>
                <em>{item.quote}</em>
              </p>
            )}
          </div>
        </article>
      ))}
    </div>
  );
}
