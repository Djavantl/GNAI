import CardBox from "@/components/UI/CardBox/CardBox";
import ImageDisplay from "@/components/UI/ImageDisplay/ImageDisplay";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import Button from "@/components/UI/Button/Button";
import RecordActions from "@/components/UI/RecordActions/RecordActions";
import type { BusinessRecord } from "@/types/BusinessRecord.types";
import "./LeadCard.css";
export default function LeadCard({
  record,
}: {
  record: BusinessRecord;
}): React.JSX.Element {
  return (
    <CardBox className="leadCard">
      <article className="leadCard__row">
        <div>
          <ImageDisplay
            src={record.avatar}
            alt={record.name}
            shape="circle"
            size="lg"
            className="leadCard__avatar"
          />
        </div>
        <div className="leadCard__member">
          <h4>
            <b>{record.name}</b>
          </h4>
          <p>
            <b>Categoria:</b> <span>{record.category}</span>
          </p>
        </div>
        <div className="leadCard__status">
          <TableLabel tone={record.tone}>{record.status}</TableLabel>
        </div>
        <div>
          <Button href="#" size="sm" tone="primary" label="Atribuir" />{" "}
          <Button href="#" size="sm" tone="primary" label="Ligar" />{" "}
          <Button href="#" size="sm" tone="primary" label="E-mail" />
        </div>
        <div>
          <RecordActions name={record.name} />
        </div>
      </article>
    </CardBox>
  );
}
