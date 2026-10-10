import { useId } from "react";
import CardBox from "@/Components/UI/CardBox/CardBox";
import ChoiceControl from "@/Components/Forms/ChoiceControl/ChoiceControl";
import TableLabel from "@/Components/UI/TableLabel/TableLabel";
import RecordActions from "@/Components/UI/RecordActions/RecordActions";
import type { BusinessRecord } from "@/types/BusinessRecord.types";
import "./OpportunityCard.css";
export default function OpportunityCard({
  record,
  last = false,
}: {
  record: BusinessRecord;
  last?: boolean;
}): JSX.Element {
  const id = useId();
  return (
    <CardBox
      className={`opportunityCard${last ? " opportunityCard--last" : ""}`}
    >
      <article className="opportunityCard__row">
        <div className="opportunityCard__selection">
          <ChoiceControl
            type="checkbox"
            tone="primary"
            id={id}
            aria-label={`Selecionar ${record.name}`}
          />
        </div>
        <div className="opportunityCard__member">
          <h4>
            <b>{record.name}</b>
          </h4>
          <p>
            <b>Categoria:</b> <span>{record.category}</span>
          </p>
          <p>
            <b>Ativo:</b> <span>{record.active}</span>
          </p>
        </div>
        <div className="opportunityCard__contact">
          <p>
            <b>E-mail:</b> <span>{record.email}</span>
          </p>
          <p>
            <b>Contato:</b> <span>{record.phone}</span>
          </p>
        </div>
        <div className="opportunityCard__status">
          <TableLabel tone={record.tone}>{record.status}</TableLabel>
        </div>
        <div>
          <RecordActions name={record.name} />
        </div>
      </article>
    </CardBox>
  );
}
