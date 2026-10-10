import { useId, useState } from "react";
import TextInput from "@/Components/Forms/TextInput/TextInput";
import Button from "@/Components/UI/Button/Button";
import Modal from "@/Components/UI/Modal/Modal";
import ContactCard from "@/Components/UI/ContactCard/ContactCard";
import ContactForm from "@/Components/Forms/ContactForm/ContactForm";
import { contacts } from "./ContactDirectory.data";
import "./ContactDirectory.css";
export default function ContactDirectory(): JSX.Element {
  const [open, setOpen] = useState(false);
  const id = useId();
  return (
    <>
      <div className="contactDirectory__toolbar">
        <div>
          <form
            className="contactDirectory__search"
            onSubmit={(event) => event.preventDefault()}
          >
            <TextInput
              id={id}
              name="search"
              placeholder="Pesquisar..."
              aria-label="Pesquisar contatos"
            />
            <Button
              tone="white"
              type="submit"
              waves={false}
              aria-label="Pesquisar contatos"
            >
              <i className="fa fa-search" />
            </Button>
          </form>
        </div>
        <div>
          <Button
            tone="default"
            className="contactDirectory__addButton"
            leftIconClass="md md-add"
            label="Adicionar contato"
            onClick={() => setOpen(true)}
          />
        </div>
      </div>
      <div className="contactDirectory__grid">
        {contacts.map((contact) => (
          <div key={contact.id}>
            <ContactCard contact={contact} />
          </div>
        ))}
      </div>
      <Modal
        open={open}
        title="Adicionar contato"
        effect="fadein"
        className="contactFormDialog"
        onClose={() => setOpen(false)}
      >
        <ContactForm onCancel={() => setOpen(false)} />
      </Modal>
    </>
  );
}
