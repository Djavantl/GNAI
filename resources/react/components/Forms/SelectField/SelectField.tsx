import { useEffect, useLayoutEffect, useRef, useState, type SelectHTMLAttributes } from "react";
import { loadExternalScript } from "@/utils/loadExternalScript";
import { loadExternalStylesheet } from "@/utils/loadExternalStylesheet";
import { joinClassNames } from "@/utils/joinClassNames";
import type { SelectFieldCollection, SelectFieldGroup, SelectFieldOption, SelectFieldRuntime } from "./SelectField.types";
import "@/components/Forms/TextInput/TextInput.css";
import "./SelectField.css";
export default function SelectField({
  id,
  name,
  groups = [],
  options,
  label,
  defaultValue,
  enhanced = true,
  placeholder = "Selecione",
  ...props
}: {
  id: string;
  name: string;
  groups?: SelectFieldGroup[];
  options?: SelectFieldOption[];
  label: string;
  defaultValue?: SelectHTMLAttributes<HTMLSelectElement>['defaultValue'];
  enhanced?: boolean;
  placeholder?: string;
} & Omit<SelectHTMLAttributes<HTMLSelectElement>, 'children'>): React.JSX.Element {
  const ref = useRef<HTMLSelectElement>(null);
  const plugin = useRef<SelectFieldCollection | null>(null);
  const nativeDisabled = useRef(props.disabled);
  nativeDisabled.current = props.disabled;
  const [loadFailed, setLoadFailed] = useState(false);
  const [nativeError, setNativeError] = useState('');
  const invalid =
    props['aria-invalid'] === true || props['aria-invalid'] === 'true';
  // Select2 owns its visible controls; keep them aligned with the native React select.
  const synchronize = () => {
    const element = ref.current;
    const instance = plugin.current;
    if (!element || !instance) return;
    element.disabled = Boolean(nativeDisabled.current);
    const disabled = element.matches(':disabled');
    instance.select2("val", element.multiple
      ? Array.from(element.selectedOptions, option => option.value) : element.value, false);
    instance.select2("enable", !disabled);
    const container = element.previousElementSibling;
    const controls = [...(container?.querySelectorAll<HTMLElement>('.select2-focusser, .select2-input, .select2-choice') ?? [])];
    const dropdown = instance.data('select2')?.dropdown[0];
    controls.push(...(dropdown?.querySelectorAll<HTMLElement>('.select2-input') ?? []));
    for (const control of controls) {
      control.setAttribute('aria-label', element.getAttribute('aria-label') ?? '');
      control.setAttribute('aria-disabled', String(disabled));
      control.setAttribute('aria-required', String(element.required));
      for (const attribute of ['aria-invalid', 'aria-describedby']) {
        const value = element.getAttribute(attribute);
        if (value) control.setAttribute(attribute, value);
        else control.removeAttribute(attribute);
      }
    }
  };
  useLayoutEffect(synchronize);
  useEffect(() => {
    if (!enhanced) return;
    setLoadFailed(false);
    let disposed = false;
    let cleanup: (() => void) | undefined;
    let dropdownCloseTimer: number | undefined;
    const element = ref.current;
    const initialize = async (): Promise<void> => {
      await loadExternalStylesheet("/assets/react/plugins/select2/select2.css");
      await loadExternalScript("/assets/react/js/jquery.min.js");
      await loadExternalScript("/assets/react/plugins/select2/select2.min.js");
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
      plugin.current = instance;
      const dropdownElement = instance.data("select2")?.dropdown[0];
      const animateDropdownClose = (): void => {
        if (!dropdownElement) return;
        window.clearTimeout(dropdownCloseTimer);
        dropdownElement.style.display = "block";
        dropdownElement.classList.remove("selectFieldDropdown--closing");
        void dropdownElement.offsetHeight;
        dropdownElement.classList.add("selectFieldDropdown--closing");
        dropdownCloseTimer = window.setTimeout(() => {
          dropdownElement.style.display = "none";
          dropdownElement.classList.remove("selectFieldDropdown--closing");
        }, window.matchMedia("(prefers-reduced-motion: reduce)").matches ? 1 : 250);
      };
      const cancelDropdownClose = (): void => {
        window.clearTimeout(dropdownCloseTimer);
        dropdownElement?.classList.remove("selectFieldDropdown--closing");
      };
      instance.on("select2-close.gnaiSelect", animateDropdownClose);
      instance.on("select2-opening.gnaiSelect", cancelDropdownClose);
      // jQuery's synthetic change does not reach React's native event delegation.
      // Forward it once; native changes already have originalEvent.
      instance.on('change.gnaiSelect', event => {
        if (element.validity.valid) setNativeError('');
        if (!event.originalEvent) element.dispatchEvent(new Event('change', { bubbles: true }));
      });
      const focus = () => { if (!element.matches(':disabled')) instance.select2('focus'); };
      const invalid = (event: Event) => {
        event.preventDefault();
        setNativeError(element.validationMessage);
        focus();
      };
      const labels = Array.from(element.labels ?? []);
      labels.forEach(label => label.addEventListener('click', focus));
      element.addEventListener('focus', focus);
      element.addEventListener('invalid', invalid);
      cleanup = () => {
        window.clearTimeout(dropdownCloseTimer);
        plugin.current = null;
        labels.forEach(label => label.removeEventListener('click', focus));
        element.removeEventListener('focus', focus);
        element.removeEventListener('invalid', invalid);
        instance.off('.gnaiSelect');
        instance.select2("destroy");
      };
      synchronize();
    };
    void initialize().catch(() => {
      if (!disposed) {
        cleanup?.();
        cleanup = undefined;
        setLoadFailed(true);
      }
    });
    return () => {
      disposed = true;
      cleanup?.();
    };
  }, [enhanced, id, props.multiple]);
  return (
    <div
      className={joinClassNames(
        "selectField",
        invalid || nativeError ? "selectField--invalid" : undefined,
      )}
    >
      <select
        {...props}
        ref={ref}
        id={id}
        name={name}
        aria-label={label}
        aria-invalid={nativeError ? true : props['aria-invalid']}
        aria-describedby={[props['aria-describedby'], nativeError ? `${id}-validation` : undefined].filter(Boolean).join(' ') || undefined}
        className="textInput"
        defaultValue={props.value === undefined ? defaultValue ?? (props.multiple ? [] : "") : undefined}
      >
        <option value="">{placeholder}</option>
        {(options ?? groups.flatMap(group => group.options)).map((option) => (
          <option value={option.value} key={option.value}>{option.label}</option>
        ))}
      </select>
      {nativeError ? <span id={`${id}-validation`} className="selectField__error" role="alert">{nativeError}</span> : null}
      {loadFailed ? <span className="selectField__fallback" role="status">Não foi possível carregar a busca. Use a seleção acima.</span> : null}
    </div>
  );
}
