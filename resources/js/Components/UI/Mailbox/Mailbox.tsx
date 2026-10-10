import MailboxSidebar from "@/Components/UI/MailboxSidebar/MailboxSidebar";
import MailToolbar from "@/Components/UI/MailToolbar/MailToolbar";
import MailList from "@/Components/UI/MailList/MailList";
import MailMessage from "@/Components/UI/MailMessage/MailMessage";
import MailCompose from "@/Components/UI/MailCompose/MailCompose";
import "./Mailbox.css";
export default function Mailbox({
  kind,
}: {
  kind: "inbox" | "email-read" | "email-compose";
}): JSX.Element {
  return (
    <div className="mailbox">
      <aside className="mailbox__sidebar">
        <MailboxSidebar
          composing={kind === "email-compose"}
          inboxPage={kind === "inbox"}
        />
      </aside>
      <div className="mailbox__content">
        {kind !== "email-compose" && <MailToolbar />}
        {kind === "inbox" ? (
          <MailList />
        ) : kind === "email-read" ? (
          <MailMessage />
        ) : (
          <MailCompose />
        )}
      </div>
    </div>
  );
}
