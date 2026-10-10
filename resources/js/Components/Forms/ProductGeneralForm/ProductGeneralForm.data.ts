import type { SelectFieldGroup } from "@/Components/Forms/SelectField/SelectField.types";
export const generalFields = [
  {
    name: "name",
    label: "Nome do produto",
    placeholder: "Ex.: Apple iMac",
    requiredIndicator: true,
    rows: 0,
  },
  {
    name: "reference",
    label: "Referência",
    placeholder: "Ex.: Apple iMac",
    requiredIndicator: true,
    rows: 0,
  },
  {
    name: "description",
    label: "Descrição do produto",
    placeholder: "Digite a descrição",
    requiredIndicator: true,
    rows: 5,
  },
  {
    name: "summary",
    label: "Resumo do produto",
    placeholder: "Digite o resumo",
    requiredIndicator: false,
    rows: 3,
  },
];
export const categoryGroups: SelectFieldGroup[] = [
  {
    label: "Compras",
    options: [1, 2, 3, 4].map((n) => ({
      value: `SH${n}`,
      label: `Compras ${n}`,
    })),
  },
  {
    label: "CRM",
    options: [1, 2, 3, 4].map((n) => ({ value: `CRM${n}`, label: `CRM ${n}` })),
  },
  {
    label: "eCommerce",
    options: [1, 2, 3, 4].map((n) => ({
      value: `E${n}`,
      label: `eCommerce ${n}`,
    })),
  },
];
export const productStatuses = ["Online", "Offline", "Rascunho"];
export const initialProductStatus = "Online";
