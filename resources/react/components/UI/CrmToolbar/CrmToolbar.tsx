import { useState } from "react";
import ContactSearch from "@/components/UI/ContactSearch/ContactSearch";
import Button from "@/components/UI/Button/Button";
import Modal from "@/components/UI/Modal/Modal";
import CrmEntryForm from "@/components/Forms/CrmEntryForm/CrmEntryForm";
import type { CrmEntryKind } from "@/components/Forms/CrmEntryForm/CrmEntryForm.types";
import "./CrmToolbar.css";
import SortButtonGroup from "@/components/UI/SortButtonGroup/SortButtonGroup";
import { sortLabels } from "./CrmToolbar.data";
export default function CrmToolbar({
  kind,
  addLabel,
  modalTitle = addLabel,
  searchLabel,
}: {
  kind: CrmEntryKind;
  addLabel: string;
  modalTitle?: string;
  searchLabel: string;
}): React.JSX.Element {
  const [open, setOpen] = useState(false);
  const [dialogOverflow, setDialogOverflow] = useState(false);
  return (
    <>
      <div className={`crmToolbar crmToolbar--${kind}`}>
        <div className="crmToolbar__search">
          <ContactSearch label={searchLabel} />
        </div>
        <div className="crmToolbar__actions">
          <Button
            tone="default"
            size="md"
            aria-label={addLabel}
            className="crmToolbar__add"
            onClick={() => setOpen(true)}
          >
            <i className="md md-add" aria-hidden="true" />{" "}{addLabel}
          </Button>
          {kind === "opportunity" && (
            <SortButtonGroup
              type="checkbox"
              options={sortLabels}
              initialSelected={["Status"]}
            />
          )}
        </div>
      </div>
      <Modal
        open={open}
        title={modalTitle}
        effect="fadein"
        className="crmEntryDialog"
        layerClassName={`crmEntryDialogLayer${dialogOverflow ? " crmEntryDialogLayer--overflow" : ""}`}
        onClose={() => setOpen(false)}
      >
        <CrmEntryForm kind={kind} onOverflowChange={setDialogOverflow} />
      </Modal>
    </>
  );
}
