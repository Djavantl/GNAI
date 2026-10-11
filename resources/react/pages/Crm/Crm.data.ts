import type {
  BusinessRecord,
  CrmPageDefinition,
  PersonRecord,
} from "./Crm.types";
export const crmPages: Record<string, CrmPageDefinition> = {
  "crm-dashboard.html": {
    key: "crm-dashboard",
    kind: "dashboard",
    title: "Dashboard",
  },
  "crm-contact.html": {
    key: "crm-contact",
    kind: "contact",
    title: "Contatos",
    breadcrumb: "Aplicativos",
  },
  "crm-opportunities.html": {
    key: "crm-opportunities",
    kind: "opportunities",
    title: "Oportunidades",
    breadcrumb: "CRM",
  },
  "crm-leads.html": {
    key: "crm-leads",
    kind: "leads",
    title: "Leads",
    breadcrumb: "CRM",
  },
  "crm-customers.html": {
    key: "crm-customers",
    kind: "customers",
    title: "Clientes",
    breadcrumb: "Aplicativos",
  },
};
const businessSources: Pick<
  BusinessRecord,
  "id" | "name" | "avatar" | "status" | "tone"
>[] = [
  {
    id: "envato",
    name: "Envato Market Pty Ltd.",
    avatar: "envato.jpg",
    status: "Quente",
    tone: "info",
  },
  {
    id: "coca-cola",
    name: "The Coca-Cola Company",
    avatar: "coco-cola.jpg",
    status: "Frio",
    tone: "purple",
  },
  {
    id: "dribbble",
    name: "Dribbble LLC",
    avatar: "dribbble-1.jpg",
    status: "Em andamento",
    tone: "warning",
  },
  {
    id: "coderthemes",
    name: "Coderthemes design",
    avatar: "coder.jpg",
    status: "Perdido",
    tone: "danger",
  },
  {
    id: "wrap",
    name: "WrapMarket LLC",
    avatar: "wrap.jpg",
    status: "Ganho",
    tone: "success",
  },
];
export const businessRecords: BusinessRecord[] = businessSources.map(
  (record) => ({
    ...record,
    avatar: `/assets/react/images/brand/${record.avatar}`,
    category: "Gerente de filial",
    email: "coderthemes@gmail.com",
    phone: "+12 34567890",
    active: "Há 2 horas",
  }),
);
export const opportunities: BusinessRecord[] = businessRecords.map(
  (record, index) => ({
    ...record,
    name:
      index === 0
        ? "Enveto Market Pty Ltd."
        : index === 4
          ? "WrapMarket LLC"
          : record.name,
  }),
);
export const leads: BusinessRecord[] = [
  ...businessRecords,
  { ...businessRecords[0], id: "envato-2" },
  { ...businessRecords[1], id: "coca-cola-2" },
];
const people: Omit<PersonRecord, "id">[] = [
  {
    name: "Tomaslau",
    email: "tomaslau@dummy.com",
    date: "01/11/2003",
    avatar: "2",
  },
  {
    name: "Chadengle",
    email: "chadengle@dummy.com",
    date: "01/11/2003",
    avatar: "1",
  },
  {
    name: "Stillnotdavid",
    email: "stillnotdavid@dummy.com",
    date: "12/11/2003",
    avatar: "3",
  },
  {
    name: "Kurafire",
    email: "kurafire@dummy.com",
    date: "14/11/2003",
    avatar: "4",
  },
  {
    name: "Shahedk",
    email: "shahedk@dummy.com",
    date: "20/11/2003",
    avatar: "5",
  },
  {
    name: "Adhamdannaway",
    email: "adhamdannaway@dummy.com",
    date: "24/11/2003",
    avatar: "6",
  },
  { name: "Ok", email: "nicename@dummy.com", date: "29/11/2003", avatar: "7" },
  {
    name: "Arashasghari",
    email: "arashasghari@dummy.com",
    date: "01/12/2003",
    avatar: "8",
  },
  {
    name: "Joshaustin",
    email: "joshaustin@dummy.com",
    date: "09/12/2003",
    avatar: "9",
  },
  {
    name: "Sortino",
    email: "sortino@dummy.com",
    date: "10/12/2003",
    avatar: "10",
  },
];
export const crmPeople: PersonRecord[] = [...people, ...people.slice(0, 4)].map(
  (person, index) => ({
    ...person,
    id: `person-${index}`,
    avatar: `/assets/react/images/users/avatar-${person.avatar}.jpg`,
    checked: index === 0,
    active: index === 0,
  }),
);
