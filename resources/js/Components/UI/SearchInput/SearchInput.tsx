import TextInput from "@/Components/Forms/TextInput/TextInput";
import Button from "@/Components/UI/Button/Button";
import "./SearchInput.css";
export default function SearchInput({
  placeholder = "Buscar...",
  onSearch,
}: {
  placeholder?: string;
  onSearch?: (query: string) => void;
}): JSX.Element {
  return (
    <form
      className="searchInput"
      onSubmit={(event) => {
        event.preventDefault();
        const data = new FormData(event.currentTarget);
        onSearch?.(String(data.get("query") ?? ""));
      }}
    >
      <TextInput
        name="query"
        placeholder={placeholder}
        aria-label={placeholder}
      />
      <Button
        type="submit"
        tone="default"
        size="lg"
        leftIconClass="fa fa-search"
        label="Buscar"
      />
    </form>
  );
}
