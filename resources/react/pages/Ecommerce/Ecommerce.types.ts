export type EcommerceKind =
  "dashboard" | "products" | "product-detail" | "orders" | "sellers";
export interface EcommercePageDefinition {
  key: string;
  kind: EcommerceKind;
  title: string;
  breadcrumb: string[];
}
