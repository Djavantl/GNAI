import CardBox from "@/components/UI/CardBox/CardBox";
import TextInput from "@/components/Forms/TextInput/TextInput";
import Button from "@/components/UI/Button/Button";
import SummernoteEditor from "@/components/Forms/SummernoteEditor/SummernoteEditor";
import { composeInitialHtml } from "./MailCompose.data";
import "./MailCompose.css";
export default function MailCompose(): React.JSX.Element {
  return (
    <CardBox className="mailCompose">
      <form
        className="mailCompose__form"
        onSubmit={(event) => event.preventDefault()}
      >
        <div className="mailCompose__field">
          <TextInput
            type="email"
            name="to"
            aria-label="Para"
            placeholder="Para"
          />
        </div>
        <div className="mailCompose__field mailCompose__copies">
          <div>
            <TextInput
              type="email"
              name="cc"
              aria-label="Cc"
              placeholder="Cc"
            />
          </div>
          <div>
            <TextInput
              type="email"
              name="bcc"
              aria-label="Cco"
              placeholder="Cco"
            />
          </div>
        </div>
        <div className="mailCompose__field">
          <TextInput
            name="subject"
            aria-label="Assunto"
            placeholder="Assunto"
          />
        </div>
        <div className="mailCompose__field">
          <SummernoteEditor
            defaultValue={composeInitialHtml}
            height={350}
            label="Mensagem do e-mail"
          />
        </div>
        <div className="mailCompose__actions">
          <div className="mailCompose__actionsGroup">
            <Button tone="success" aria-label="Salvar rascunho">
              <i className="fa fa-floppy-o" />
            </Button>{" "}
            <Button tone="success" aria-label="Descartar rascunho">
              <i className="fa fa-trash-o" />
            </Button>{" "}
            <Button tone="purple" type="submit">
              Enviar <i className="fa fa-send mailCompose__sendIcon" />
            </Button>
          </div>
        </div>
      </form>
    </CardBox>
  );
}
