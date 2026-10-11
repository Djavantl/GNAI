import type { SidebarItem } from "./SidebarPrimary.types";

export const multiLevelMenu: SidebarItem = {
  key: "multi-level",
  label: "Multi Nível",
  href: "#",
  iconClass: "ti-share",
  children: [
    {
      key: "multi-level-1-1",
      label: "Nível de menu 1.1",
      href: "#",
      waves: true,
      children: [
        { key: "multi-level-2-1", label: "Nível de menu 2.1", href: "#" },
        { key: "multi-level-2-2", label: "Nível de menu 2.2", href: "#" },
        { key: "multi-level-2-3", label: "Nível de menu 2.3", href: "#" },
      ],
    },
    { key: "multi-level-1-2", label: "Nível de menu 1.2", href: "#" },
  ],
};
