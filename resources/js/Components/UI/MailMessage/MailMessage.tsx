import CardBox from "@/Components/UI/CardBox/CardBox";
import ImageDisplay from "@/Components/UI/ImageDisplay/ImageDisplay";
import Button from "@/Components/UI/Button/Button";
import SummernoteEditor from "@/Components/Forms/SummernoteEditor/SummernoteEditor";
import { replyInitialHtml } from "@/Components/UI/MailCompose/MailCompose.data";
import { mailMessage } from "./MailMessage.data";
import "./MailMessage.css";
export default function MailMessage(): JSX.Element {
  return (
    <>
      <CardBox className="mailMessage">
        <h4 className="mailMessage__subject">
          <b>{mailMessage.subject}</b>
        </h4>
        <hr />
        <div className="mailMessage__sender">
          <a href="#">
            <ImageDisplay
              src="/assets/gnai/images/users/avatar-2.jpg"
              shape="circle"
              size="sm"
              alt="Jonathan Smith"
            />
          </a>
          <div>
            <span className="mailMessage__time">07:23 AM</span>
            <h4>Jonathan Smith</h4>
            <small>
              De:{" "}
              <span className="mailMessage__address">jonathan@domain.com</span>
            </small>
          </div>
        </div>
        <p>
          <b>Olá, amigo...</b>
        </p>
        {mailMessage.paragraphs.map((paragraph, index) => (
          <p key={index}>{paragraph}</p>
        ))}
        <hr />
        <h4>
          <i className="fa fa-paperclip mailMessage__clip" aria-hidden="true" />
          Anexos <span>(3)</span>
        </h4>
        <div className="mailMessage__attachments">
          {mailMessage.attachments.map((src, index) => (
            <div key={src}>
              <a href="#">
                <ImageDisplay
                  src={src}
                  shape="thumbnail"
                  alt={`Anexo ${index + 1}`}
                />
              </a>
            </div>
          ))}
        </div>
      </CardBox>
      <div className="mailMessage__reply">
        <a href="#">
          <ImageDisplay
            src="/assets/gnai/images/users/avatar-9.jpg"
            shape="circle"
            size="sm"
            alt="Seu avatar"
          />
        </a>
        <CardBox className="mailMessage__replyCard">
          <SummernoteEditor
            mode="inline"
            defaultValue={replyInitialHtml}
            label="Resposta ao e-mail"
          />
        </CardBox>
      </div>
      <div className="mailMessage__send">
        <Button label="Enviar" tone="primary" width="md" />
      </div>
    </>
  );
}
