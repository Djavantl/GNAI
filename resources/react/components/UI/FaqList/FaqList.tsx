import CardBox from "@/components/UI/CardBox/CardBox";
import { faqItems } from "./FaqList.data";
import "./FaqList.css";
import type { FaqItem } from "./FaqList.types";
export default function FaqList({
  items = faqItems,
}: {
  items?: FaqItem[];
}): React.JSX.Element {
  const middle = Math.ceil(items.length / 2);
  return (
    <CardBox>
      <div className="faqList">
        {[items.slice(0, middle), items.slice(middle)].map((column, i) => (
          <div className="faqList__column" key={i}>
            <div className="faqList__inner">
              {column.map((item, j) => (
                <article className="faqList__item" key={j}>
                  <h4>{item.question}</h4>
                  <p>{item.answer}</p>
                </article>
              ))}
            </div>
          </div>
        ))}
      </div>
    </CardBox>
  );
}
