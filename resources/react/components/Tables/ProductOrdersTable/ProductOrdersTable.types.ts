export interface ProductOrderRecord {
  id: string;
  image: string;
  date: string;
  number: string;
  amount: string;
  seller?: string;
  status?: string;
  tone?: "success" | "warning" | "primary" | "danger";
}
export type ProductOrdersVariant = "orders" | "recent" | "popular";
