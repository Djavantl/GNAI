export type LayoutKey =
  | "layout-leftbar-2"
  | "layout-menu-collapsed"
  | "layout-menu-small"
  | "layout-header-2";

export interface LayoutDefinition {
  key: LayoutKey;
  title: string;
  sidebar: "default" | "user" | "small";
  initiallyCollapsed?: boolean;
  header?: "banner";
}
