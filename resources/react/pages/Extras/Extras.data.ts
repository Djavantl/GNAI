import type { ExtrasDefinition } from "./Extras.types";
export const extras: Record<string, ExtrasDefinition> = Object.fromEntries(
  [
    ["profile", "Perfil"],
    ["timeline", "Linha do tempo"],
    ["sitemap", "Mapa do site"],
    ["invoice", "Fatura"],
    ["email-template", "Modelos de e-mail"],
    ["maintenance", "Manutenção"],
    ["coming-soon", "Em breve"],
    ["faq", "Perguntas frequentes"],
    ["search-result", "Resultados da busca"],
    ["gallery", "Galeria"],
    ["pricing", "Planos e preços"],
  ].map(([kind, title]) => [
    `extra-${kind}.html`,
    {
      key: `extra-${kind}`,
      kind: kind as ExtrasDefinition["kind"],
      title,
      standalone: kind === "maintenance" || kind === "coming-soon",
    },
  ]),
);
