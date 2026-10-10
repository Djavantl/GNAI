import { useId } from "react";
import TextInput from "@/Components/Forms/TextInput/TextInput";
import Button from "@/Components/UI/Button/Button";
import "./ContactSearch.css";
export default function ContactSearch({
  label,
  onSearch,
}: {
  label: string;
  onSearch?: (query: string) => void;
}): JSX.Element {
  const id = useId();
  return (
    <form
      className="contactSearch"
      onSubmit={(event) => {
        event.preventDefault();
        onSearch?.(
          String(new FormData(event.currentTarget).get("query") ?? ""),
        );
      }}
    >
      <TextInput
        id={id}
        name="query"
        placeholder="Pesquisar..."
        aria-label={label}
      />
      <Button type="submit" tone="white" waves={false} aria-label={label}>
        <i className="fa fa-search" aria-hidden="true" />
      </Button>
    </form>
  );
}
