import { useState } from "react";
import Panel from "@/Components/UI/Panel/Panel";
import Table from "@/Components/Tables/Table/Table";
import ChoiceControl from "@/Components/Forms/ChoiceControl/ChoiceControl";
import Button from "@/Components/UI/Button/Button";
import { mailSummaries } from "./MailList.data";
import type { MailSummary } from "./MailList.types";
import "./MailList.css";
export default function MailList({
  messages = mailSummaries,
}: {
  messages?: MailSummary[];
}): JSX.Element {
  const [selected, setSelected] = useState<string[]>([]);
  return (
    <>
      <Panel className="mailList">
        <div className="mailList__scroll">
          <Table hover className="mailList__table">
            <tbody>
              {messages.map((message) => (
                <tr
                  key={message.id}
                  className={message.unread ? "is-unread" : ""}
                >
                  <td className="mailList__selection">
                    <ChoiceControl
                      type="checkbox"
                      id={`mail-${message.id}`}
                      label={
                        <span className="mailList__sr">
                          Selecionar e-mail de {message.sender}
                        </span>
                      }
                      tone="primary"
                      checked={selected.includes(message.id)}
                      onChange={(checked) =>
                        setSelected((values) =>
                          checked
                            ? [...values, message.id]
                            : values.filter((id) => id !== message.id),
                        )
                      }
                    />
                    <i
                      className={`fa fa-${message.starred ? "star" : "star-o"} mailList__star`}
                      aria-hidden="true"
                    />
                    <i
                      className={`fa fa-circle mailList__marker mailList__marker--${message.tone}`}
                      aria-hidden="true"
                    />
                  </td>
                  <td>
                    <a href="apps-email-read.html" className="mailList__sender">
                      {message.sender}
                    </a>
                  </td>
                  <td>
                    <a
                      href="apps-email-read.html"
                      className="mailList__subject"
                    >
                      {message.subject}
                    </a>
                  </td>
                  <td className="mailList__attachment">
                    {message.attachment && (
                      <i className="fa fa-paperclip" aria-label="Com anexo" />
                    )}
                  </td>
                  <td className="mailList__date">{message.date}</td>
                </tr>
              ))}
            </tbody>
          </Table>
        </div>
      </Panel>
      <div className="mailList__pagination">
        <span>Exibindo 1 - 20 de 289</span>
        <div className="mailList__pageButtons">
          <Button
            tone="default"
            wavesLight={false}
            aria-label="Página anterior"
          >
            <i className="fa fa-chevron-left" />
          </Button>
          <Button tone="default" wavesLight={false} aria-label="Próxima página">
            <i className="fa fa-chevron-right" />
          </Button>
        </div>
      </div>
    </>
  );
}
