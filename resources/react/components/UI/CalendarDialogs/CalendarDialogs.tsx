import { useId, useRef, useState } from "react";
import Modal from "@/components/UI/Modal/Modal";
import Button from "@/components/UI/Button/Button";
import TextInput from "@/components/Forms/TextInput/TextInput";
import { calendarColors } from "@/components/UI/CalendarCategories/CalendarCategories.data";
import type { CalendarTone } from "@/components/UI/EventCalendar/EventCalendar.types";
import type { CalendarDialogsProps } from "./CalendarDialogs.types";
import "./CalendarDialogs.css";
export default function CalendarDialogs(
  props: CalendarDialogsProps,
): React.JSX.Element {
  const { dialog: incomingDialog, onClose } = props;
  const lastDialog = useRef(incomingDialog);
  if (incomingDialog) lastDialog.current = incomingDialog;
  const dialog = lastDialog.current;
  const id = useId();
  const [title, setTitle] = useState(
    dialog?.kind === "event" ? dialog.event.title : "",
  );
  const [tone, setTone] = useState<CalendarTone>(
    dialog?.kind === "category" ? "success" : "danger",
  );
  function save(): void {
    if (!incomingDialog || !dialog) return;
    if (dialog.kind === "category") {
      if (title) props.onCreateCategory(title, tone);
      onClose();
    } else if (dialog.kind === "event") {
      props.onUpdateEvent(dialog.event, title);
      onClose();
    } else if (title) {
      props.onCreateEvent(dialog.selection, title, tone);
      onClose();
    } else window.alert("Você precisa dar um título ao evento");
  }
  return (
    <Modal
      open={incomingDialog !== null}
      title={
        dialog?.kind === "category" ? (
          <>
            <strong>Adicionar</strong> categoria
          </>
        ) : (
          <strong>Adicionar evento</strong>
        )
      }
      bootstrap
      closeOnBackdrop={dialog?.kind === "category"}
      className="calendarDialog"
      onClose={onClose}
      footer={
        <>
          <Button label="Fechar" tone="white" onClick={onClose} />
          {dialog?.kind === "category" ? (
            <Button label="Salvar" tone="danger" onClick={save} />
          ) : dialog?.kind === "event" ? (
            <Button
              label="Excluir"
              tone="danger"
              onClick={() => {
                props.onDeleteEvent(dialog.event);
                onClose();
              }}
            />
          ) : (
            <Button label="Criar evento" tone="success" onClick={save} />
          )}
        </>
      }
    >
      <form
        onSubmit={(event) => {
          event.preventDefault();
          save();
        }}
      >
        {dialog?.kind === "event" ? (
          <>
            <label htmlFor={`${id}-name`}>Alterar nome do evento</label>
            <div className="calendarDialog__edit">
              <TextInput
                id={`${id}-name`}
                value={title}
                onChange={(event) => setTitle(event.target.value)}
              />
              <Button tone="success" type="submit">
                <i className="fa fa-check" /> Salvar
              </Button>
            </div>
          </>
        ) : (
          <div className="calendarDialog__fields">
            <div>
              <label htmlFor={`${id}-name`}>
                {dialog?.kind === "category"
                  ? "Nome da categoria"
                  : "Nome do evento"}
              </label>
              <TextInput
                id={`${id}-name`}
                placeholder={
                  dialog?.kind === "category"
                    ? "Digite o nome"
                    : "Digite o nome do evento"
                }
                value={title}
                onChange={(event) => setTitle(event.target.value)}
              />
            </div>
            <div>
              <label htmlFor={`${id}-color`}>
                {dialog?.kind === "category"
                  ? "Escolha a cor da categoria"
                  : "Categoria"}
              </label>
              <select
                id={`${id}-color`}
                className="calendarDialog__select"
                value={tone}
                onChange={(event) =>
                  setTone(event.target.value as CalendarTone)
                }
              >
                {(dialog?.kind === "category"
                  ? calendarColors.filter((color) => color.value !== "purple")
                  : [
                      calendarColors[1],
                      calendarColors[0],
                      calendarColors[7],
                      calendarColors[4],
                      calendarColors[3],
                      calendarColors[2],
                      calendarColors[5],
                    ]
                ).map((color) => (
                  <option key={color.value} value={color.value}>
                    {color.label}
                  </option>
                ))}
              </select>
            </div>
          </div>
        )}
      </form>
    </Modal>
  );
}
