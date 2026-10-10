import { useEffect, useRef } from "react";
import { loadExternalScript } from "@/utils/loadExternalScript";
import { loadExternalStylesheet } from "@/utils/loadExternalStylesheet";
import type { SelectFieldGroup, SelectFieldRuntime } from "./SelectField.types";
import "@/Components/Forms/TextInput/TextInput.css";
import "./SelectField.css";
export default function SelectField({
  id,
  name,
  groups,
  label,
  defaultValue = "",
}: {
  id: string;
  name: string;
  groups: SelectFieldGroup[];
  label: string;
  defaultValue?: string;
}): JSX.Element {
  const ref = useRef<HTMLSelectElement>(null);
  useEffect(() => {
    let disposed = false;
    let cleanup: (() => void) | undefined;
    const element = ref.current;
    const initialize = async (): Promise<void> => {
      await loadExternalStylesheet("/assets/gnai/plugins/select2/select2.css");
      await loadExternalScript("/assets/gnai/js/jquery.min.js");
      await loadExternalScript("/assets/gnai/plugins/select2/select2.min.js");
      if (disposed || !element) return;
      const runtime = (window as unknown as { jQuery: SelectFieldRuntime })
        .jQuery;
      const instance = runtime(element);
      instance.select2({
        width: "100%",
        dropdownCssClass: "selectFieldDropdown",
        searchInputPlaceholder: "Pesquisar...",
        formatNoMatches: () => "Nenhum resultado encontrado",
        formatSearching: () => "Pesquisando...",
        formatMatches: (count: number) =>
          `${count} resultado(s). Use as setas para navegar e Enter para selecionar.`,
      });
      cleanup = () => {
        instance.select2("destroy");
      };
    };
    void initialize().catch(console.error);
    return () => {
      disposed = true;
      cleanup?.();
    };
  }, []);
  return (
    <div className="selectField">
      <select
        ref={ref}
        id={id}
        name={name}
        aria-label={label}
        className="textInput"
        defaultValue={defaultValue}
      >
        <option value="">Selecione</option>
        {groups.map((group) => (
          <optgroup label={group.label} key={group.label}>
            {group.options.map((option) => (
              <option value={option.value} key={option.value}>
                {option.label}
              </option>
            ))}
          </optgroup>
        ))}
      </select>
    </div>
  );
}
