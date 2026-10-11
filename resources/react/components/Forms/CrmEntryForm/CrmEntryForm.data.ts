import type { CrmEntryField, CrmEntryKind } from "./CrmEntryForm.types";
const name: CrmEntryField = {
  name: "name",
  label: "Nome",
  placeholder: "Digite o nome",
};
const email: CrmEntryField = {
  name: "email",
  label: "Endereço de e-mail",
  placeholder: "Digite o e-mail",
  type: "email",
};
const phone: CrmEntryField = {
  name: "phone",
  label: "Número de contato",
  placeholder: "Digite o número",
};
const category: CrmEntryField = {
  name: "category",
  label: "Categoria",
  placeholder: "Digite a categoria",
};
export const crmEntryFields: Record<CrmEntryKind, CrmEntryField[]> = {
  person: [name, email, phone],
  opportunity: [name, category, phone],
  lead: [
    name,
    category,
    phone,
    email,
    { name: "status", label: "Status", placeholder: "Status" },
  ],
};
