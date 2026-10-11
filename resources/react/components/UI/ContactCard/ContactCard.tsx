import CardBox from "@/components/UI/CardBox/CardBox";
import ImageDisplay from "@/components/UI/ImageDisplay/ImageDisplay";
import Button from "@/components/UI/Button/Button";
import SocialLinks from "@/components/UI/SocialLinks/SocialLinks";
import type { ContactCardProps } from "./ContactCard.types";
import "./ContactCard.css";
export default function ContactCard({
  contact,
  onEdit,
  onDelete,
}: ContactCardProps): React.JSX.Element {
  return (
    <CardBox>
      <article className="contactCard">
        <a href="#" className="contactCard__avatar">
          <ImageDisplay
            shape="circle"
            src={contact.avatar}
            alt={contact.name}
            width={80}
          />
        </a>
        <div className="contactCard__info">
          <h4>
            <b>{contact.name}</b>
          </h4>
          <p className="contactCard__position">{contact.position}</p>
          <p className="contactCard__company">
            <i className="md md-business" aria-hidden="true" />
            <small>{contact.company}</small>
          </p>
          <div className="contactCard__actions">
            <Button
              href="#"
              tone="success"
              size="sm"
              waves={false}
              aria-label={`Editar ${contact.name}`}
              onClick={() => onEdit?.(contact)}
            >
              <i className="md md-mode-edit" />
            </Button>{" "}
            <Button
              href="#"
              tone="danger"
              size="sm"
              waves={false}
              aria-label={`Excluir ${contact.name}`}
              onClick={() => onDelete?.(contact)}
            >
              <i className="md md-close" />
            </Button>
          </div>
        </div>
        <SocialLinks />
      </article>
    </CardBox>
  );
}
