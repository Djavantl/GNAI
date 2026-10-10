import { useId } from "react";
import Button from "@/Components/UI/Button/Button";
import TextInput from "@/Components/Forms/TextInput/TextInput";
import { contactFields } from "./ContactForm.data";
import type { ContactFormProps, ContactFormValues } from "./ContactForm.types";
import "./ContactForm.css";
export default function ContactForm({
  onCancel,
  onSave,
}: ContactFormProps): JSX.Element {
  const id = useId();
  return (
    <form
      className="contactForm"
      onSubmit={(event) => {
        event.preventDefault();
        const data = new FormData(event.currentTarget);
        const values: ContactFormValues = {
          name: String(data.get("name") ?? ""),
          position: String(data.get("position") ?? ""),
          company: String(data.get("company") ?? ""),
          email: String(data.get("email") ?? ""),
        };
        onSave?.(values);
      }}
    >
      {contactFields.map((field) => (
        <div className="contactForm__field" key={field.name}>
          <label htmlFor={`${id}-${field.name}`}>{field.label}</label>
          <TextInput
            id={`${id}-${field.name}`}
            name={field.name}
            type={field.type}
            placeholder={field.placeholder}
          />
        </div>
      ))}
      <Button label="Salvar" tone="default" type="submit" />{" "}
      <Button
        label="Cancelar"
        tone="danger"
        className="contactForm__cancel"
        onClick={onCancel}
      />
    </form>
  );
}
