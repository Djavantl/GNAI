import type { LayoutDefinition } from "./Layouts.types";

export const layouts: Record<string, LayoutDefinition> = {
  "layout-leftbar_2.html": {
    key: "layout-leftbar-2",
    title: "Barra lateral com usuário",
    sidebar: "user",
  },
  "layout-menu-collapsed.html": {
    key: "layout-menu-collapsed",
    title: "Menu recolhido",
    sidebar: "default",
    initiallyCollapsed: true,
  },
  "layout-menu-small.html": {
    key: "layout-menu-small",
    title: "Menu pequeno",
    sidebar: "small",
  },
  "layout-header_2.html": {
    key: "layout-header-2",
    title: "Estilo do cabeçalho",
    sidebar: "default",
    header: "banner",
  },
};
