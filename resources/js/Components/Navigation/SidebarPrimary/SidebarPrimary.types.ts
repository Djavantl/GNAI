export type SidebarLabelVariant = "primary" | "pink" | "success";

export interface SidebarLabel {
  text: string;
  variant: SidebarLabelVariant;
}

export interface SidebarItem {
  key: string;
  label: string;
  href?: string;
  iconClass?: string;
  children?: SidebarItem[];
  labelBadge?: SidebarLabel;
  waves?: boolean;
}

export interface SidebarSectionTitle {
  key: string;
  title: string;
}

export type SidebarEntry = SidebarSectionTitle | SidebarItem;
