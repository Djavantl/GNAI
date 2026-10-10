import type { ApplicationDefinition } from "./Applications.types";
export const applications: Record<string, ApplicationDefinition> = {
  "apps-inbox.html": {
    key: "apps-inbox",
    kind: "inbox",
    title: "Caixa de entrada",
  },
  "apps-email-read.html": {
    key: "apps-inbox",
    kind: "email-read",
    title: "Ler e-mail",
  },
  "apps-email-compose.html": {
    key: "apps-inbox",
    kind: "email-compose",
    title: "Escrever e-mail",
  },
  "apps-calendar.html": {
    key: "apps-calendar",
    kind: "calendar",
    title: "Calendário",
  },
  "apps-contact.html": {
    key: "apps-contact",
    kind: "contact",
    title: "Contatos",
  },
};
