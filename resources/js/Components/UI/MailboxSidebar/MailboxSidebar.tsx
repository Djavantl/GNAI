import Button from "@/Components/UI/Button/Button";
import { mailFolders, mailLabels } from "./MailboxSidebar.data";
import "./MailboxSidebar.css";
export default function MailboxSidebar({
  composing = false,
  inboxPage = false,
}: {
  composing?: boolean;
  inboxPage?: boolean;
}): JSX.Element {
  return (
    <div className="mailboxSidebar">
      <Button
        href={composing ? "apps-inbox.html" : "apps-email-compose.html"}
        label={composing ? "Voltar à caixa de entrada" : "Escrever"}
        tone="danger"
        rounded
        outline
        block
      />
      <nav className="mailboxSidebar__folders" aria-label="Pastas de e-mail">
        {mailFolders.map((folder) => (
          <a
            key={folder.label}
            href={folder.active && inboxPage ? "#" : (folder.href ?? "#")}
            className={folder.active ? "is-active" : ""}
          >
            <i className={`fa fa-${folder.icon}`} aria-hidden="true" />
            {folder.label}
            {folder.count !== undefined && (
              <>
                {" "}
                <b>({folder.count})</b>
              </>
            )}
          </a>
        ))}
      </nav>
      <h3 className="mailboxSidebar__heading">Marcadores</h3>
      <nav className="mailboxSidebar__labels" aria-label="Marcadores de e-mail">
        {mailLabels.map((label) => (
          <a key={label.label} href="#">
            <i
              className={`fa fa-circle mailboxSidebar__color--${label.tone}`}
              aria-hidden="true"
            />
            {label.label}
          </a>
        ))}
      </nav>
    </div>
  );
}
