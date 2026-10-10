import { useId } from "react";
import Button from "@/Components/UI/Button/Button";
import TextInput from "@/Components/Forms/TextInput/TextInput";
import { useModalClose } from "@/Components/UI/Modal/Modal";
import { crmEntryFields } from "./CrmEntryForm.data";
import type { CrmEntryKind } from "./CrmEntryForm.types";
import "./CrmEntryForm.css";
import { useCrmDialogViewport } from "./useCrmDialogViewport";
export default function CrmEntryForm({
  kind,
  onOverflowChange,
}: {
  kind: CrmEntryKind;
  onOverflowChange: (overflow: boolean) => void;
}): JSX.Element {
  const id = useId();
  const ref = useCrmDialogViewport(onOverflowChange);
  const close = useModalClose();
  return (
    <form
      ref={ref}
      className="crmEntryForm"
      onSubmit={(event) => event.preventDefault()}
    >
      {crmEntryFields[kind].map((field) => (
        <div className="crmEntryForm__field" key={field.name}>
          <label htmlFor={`${id}-${field.name}`}>{field.label}</label>
          <TextInput
            id={`${id}-${field.name}`}
            name={field.name}
            type={field.type ?? "text"}
            placeholder={field.placeholder}
          />
        </div>
      ))}
      <Button type="submit" tone="default" label="Salvar" />{" "}
      <Button
        tone="danger"
        label="Cancelar"
        className="crmEntryForm__cancel"
        onClick={() => close?.()}
      />
    </form>
  );
}
