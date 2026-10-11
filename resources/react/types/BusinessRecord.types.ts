export interface BusinessRecord {
  id: string;
  name: string;
  category: string;
  status: string;
  tone: "info" | "purple" | "warning" | "danger" | "success";
  avatar: string;
  email: string;
  phone: string;
  active: string;
}
