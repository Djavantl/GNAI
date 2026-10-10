import CardBox from "@/Components/UI/CardBox/CardBox";
import ImageDisplay from "@/Components/UI/ImageDisplay/ImageDisplay";
import Button from "@/Components/UI/Button/Button";
import ActivityTimeline from "@/Components/UI/ActivityTimeline/ActivityTimeline";
import ScrollArea from "@/Components/Navigation/ScrollArea/ScrollArea";
import { crmActivity } from "./ContactActivityCard.data";
import "./ContactActivityCard.css";
export default function ContactActivityCard(): JSX.Element {
  return (
    <CardBox>
      <article className="contactActivityCard">
        <a className="contactActivityCard__avatar" href="#">
          <ImageDisplay
            src="/assets/gnai/images/users/avatar-6.jpg"
            alt="Bill Bertz"
            shape="circle"
            width={80}
          />
        </a>
        <div className="contactActivityCard__info">
          <h4>
            <b>Bill Bertz</b>
          </h4>
          <p className="contactActivityCard__position">Gerente de filial</p>
          <p>
            <i className="md md-business" aria-hidden="true" />
            <small>ABC company Pvt Ltd.</small>
          </p>
          <div className="contactActivityCard__buttons">
            <Button href="#" tone="purple" size="sm" label="Enviar e-mail" />{" "}
            <Button
              href="#"
              tone="success"
              size="sm"
              className="contactActivityCard__spaced"
              label="Editar"
            />{" "}
            <Button
              href="#"
              tone="pink"
              size="sm"
              className="contactActivityCard__spaced"
              label="Ligar"
            />
          </div>
        </div>
      </article>
      <div className="contactActivityCard__activity">
        <h4>
          <b>Atividades</b>
        </h4>
        <ScrollArea
          maxHeight={555}
          className="contactActivityCard__scroll"
          label="Atividades do contato"
        >
          <ActivityTimeline items={crmActivity} compactTime />
        </ScrollArea>
      </div>
    </CardBox>
  );
}
