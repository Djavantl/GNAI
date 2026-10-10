import Button from "@/Components/UI/Button/Button";
import DropdownMenu from "@/Components/Navigation/DropdownMenu/DropdownMenu";
import { mailActions, moreMailActions } from "./MailToolbar.data";
import "./MailToolbar.css";
export default function MailToolbar(): JSX.Element {
  return (
    <div className="mailToolbar" role="toolbar" aria-label="Ações de e-mail">
      <div className="mailToolbar__group">
        {[
          { icon: "inbox", label: "Arquivar" },
          { icon: "exclamation-circle", label: "Denunciar spam" },
          { icon: "trash-o", label: "Excluir" },
        ].map((action) => (
          <Button key={action.icon} tone="primary" aria-label={action.label}>
            <i className={`fa fa-${action.icon}`} aria-hidden="true" />
          </Button>
        ))}
      </div>
      <DropdownMenu
        label={<i className="fa fa-folder" aria-hidden="true" />}
        accessibleLabel="Mover para pasta"
        items={mailActions}
      />
      <DropdownMenu
        label={<i className="fa fa-tag" aria-hidden="true" />}
        accessibleLabel="Aplicar marcador"
        items={mailActions}
      />
      <DropdownMenu label="Mais" items={moreMailActions} />
    </div>
  );
}
